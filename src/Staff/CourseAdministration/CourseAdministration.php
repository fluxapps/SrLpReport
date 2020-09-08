<?php

namespace srag\Plugins\SrLpReport\Staff\CourseAdministration;

use ilLPStatus;
use ilMStListUser;
use ilMStListUsers;
use ilObjCourse;
use ilObjUser;
use ilOrgUnitUserAssignment;
use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class CourseAdministration
 *
 * @package srag\Plugins\SrLpReport\Staff\CourseAdministration
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class CourseAdministration
{

    use DICTrait;
    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @var ilObjCourse[]|null
     */
    protected $courses_cache = null;


    /**
     * Courses constructor
     */
    private function __construct()
    {

    }


    /**
     * @param int    $usr_id
     * @param array  $filter
     * @param string $order
     * @param string $order_direction
     * @param int    $limit_start
     * @param int    $limit_end
     *
     * @return array
     */
    public function getData(int $usr_id, array $filter, string $order, string $order_direction, int $limit_start, int $limit_end) : array
    {
        $data = [];

        $users = self::access()->getUsersForUser($usr_id);

        $filter['activation'] = "active";

        $options = [
            "filters" => $filter,
            "limit"   => [],
            "count"   => false,
            "sort"    => [
                "field"     => $order,
                "direction" => $order_direction
            ]
        ];

        $data["data"] = ilMStListUsers::getData($users, $options);

        if (!empty($filter["org_units"])) {
            $data["data"] = array_filter($data["data"], function (ilMStListUser $user) use ($filter): bool {
                $org_units = $filter["org_units"];

                if ($filter["org_units_subsequent"]) {
                    foreach ($filter["org_units"] as $org_unit_ref_id) {
                        $org_units = array_merge($org_units, self::dic()->tree()->getSubTree(self::dic()->tree()->getNodeData($org_unit_ref_id), false));
                    }
                }

                $org_units = array_unique($org_units);

                return (ilOrgUnitUserAssignment::where([
                        "user_id" => $user->getUsrId(),
                        "orgu_id" => $org_units,
                    ])->first() !== null);
            });
        }

        if (!empty($filter["enrolled_before"])) {
            $data["data"] = array_filter($data["data"], function (ilMStListUser $user) use ($filter): bool {
                foreach (array_keys($this->getCourses()) as $crs_obj_id) {
                    $enrollment = $this->getEnrollment($crs_obj_id, $user->getUsrId());
                    if ($enrollment !== null) {
                        if ($enrollment->getEnrollmentTime() < $filter["enrolled_before"]->getUnixTime()) {
                            return true;
                        }
                    }
                }

                return false;
            });
        }

        if (!empty($filter["enrolled_crs_obj_ids"])) {
            $data["data"] = array_filter($data["data"], function (ilMStListUser $user) use ($filter): bool {
                foreach ($this->getCourses() as $crs_obj_id => $crs) {
                    if (in_array($crs_obj_id, $filter["enrolled_crs_obj_ids"])) {
                        if ($crs->getMembersObject()->isAssigned($user->getUsrId())) {
                            return true;
                        }
                    }
                }

                return false;
            });
        }

        if (!empty($filter["not_enrolled_crs_obj_ids"])) {
            $data["data"] = array_filter($data["data"], function (ilMStListUser $user) use ($filter): bool {
                foreach ($this->getCourses() as $crs_obj_id => $crs) {
                    if (in_array($crs_obj_id, $filter["not_enrolled_crs_obj_ids"])) {
                        if (!$crs->getMembersObject()->isAssigned($user->getUsrId())) {
                            return true;
                        }
                    }
                }

                return false;
            });
        }

        if (!empty($filter["enrolled_lp_status"])) {
            $data["data"] = array_filter($data["data"], function (ilMStListUser $user) use ($filter): bool {
                foreach (array_keys($this->getCourses()) as $crs_obj_id) {
                    if (in_array(ilLPStatus::_lookupStatus($crs_obj_id, $user->getUsrId()), $filter["enrolled_lp_status"])) {
                        return true;
                    }
                }

                return false;
            });
        }

        foreach (Config::getField(Config::KEY_COURSE_ADMINISTRATION_UDF_FIELDS) as $field_id) {
            if (!empty($filter["udf_field_" . $field_id])) {
                $data["data"] = array_filter($data["data"],
                    /**
                     * @param ilMStListUser|ilMStListUser54 $user
                     */
                    function (/*ilMStListUser*/ $user) use ($field_id, $filter) : bool {
                        $user->{"udf_field_" . $field_id} = $user->returnIlUserObj()->getUserDefinedData()["f_" . $field_id];

                        if (!empty(array_filter($filter["udf_field_" . $field_id], function (array $value) use ($user, $field_id): bool {
                            return stripos($user->{"udf_field_" . $field_id}, $value["value"]) !== false;
                        }))) {
                            return true;
                        }

                    return false;
                });
            }
        }

        if (!empty($filter["user_language"])) {
            $data["data"] = array_filter($data["data"], function (ilMStListUser $user) use ($filter): bool {
                $user->user_language = ilObjUser::_lookupLanguage($user->getUsrId());

                if (in_array($user->user_language, $filter["user_language"])) {
                    return true;
                }

                return false;
            });
        }

        $data["max_count"] = count($data["data"]);
        $data["data"] = array_slice($data["data"], $limit_start, $limit_end);

        $data["data"] = array_map(function (ilMStListUser $user) : ilMStListUser {
            foreach (Config::getField(Config::KEY_COURSE_ADMINISTRATION_UDF_FIELDS) as $field_id) {
                if (!isset($user->{"udf_field_" . $field_id})) {
                    $user->{"udf_field_" . $field_id} = $user->returnIlUserObj()->getUserDefinedData()["f_" . $field_id];
                }
            }

            foreach ($this->getCourses() as $crs_obj_id => $crs) {
                $user->{"crs_" . $crs_obj_id} = $crs;
            }

            $user->changed_time = $this->getChangedTime($user->getUsrId());

            if (!isset($user->user_language)) {
                $user->user_language = ilObjUser::_lookupLanguage($user->getUsrId());
            }

            return $user;
        }, $data["data"]);

        return $data;
    }


    /**
     * @return ilObjCourse[]
     */
    public function getCourses() : array
    {
        if ($this->courses_cache === null) {
            $this->courses_cache = [];

            foreach (Config::getField(Config::KEY_COURSE_ADMINISTRATION_COURSES) as $crs_obj_id) {
                $this->courses_cache[$crs_obj_id] = new ilObjCourse($crs_obj_id, false);
            }
        }

        return $this->courses_cache;
    }


    /**
     * @param int[] $usr_ids
     * @param int[] $crs_obj_ids
     *
     * @return string
     */
    public function enroll(array $usr_ids, array $crs_obj_ids) : string
    {
        $result = [];

        foreach ($crs_obj_ids as $crs_obj_id) {
            $crs = $this->getCourses()[$crs_obj_id];

            $result[$crs->getId()] = [];

            foreach ($usr_ids as $usr_id) {
                if (!$crs->getMembersObject()->isAssigned($usr_id)) {
                    $crs->getMembersObject()->add($usr_id, IL_CRS_MEMBER);

                    $result[$crs->getId()][] = $usr_id;
                }
            }
        }

        $result2 = [];
        foreach ($result as $crs_obj_id => $usr_ids2) {
            $result2[self::dic()->objDataCache()->lookupTitle($crs_obj_id)] = self::output()->getHTML(self::dic()->ui()->factory()->listing()->unordered(array_map(function (int $usr_id) : string {
                return ilObjUser::_lookupLogin($usr_id);
            }, $usr_ids2)));
        }

        if (!empty(array_filter($result2))) {
            return self::output()->getHTML([self::plugin()->translate("enrolled", CourseAdministrationStaffGUI::LANG_MODULE), self::dic()->ui()->factory()->listing()->descriptive($result2)]);
        } else {
            return "";
        }
    }

    /**
     * @param int[] $usr_ids
     * @param int[] $crs_obj_ids
     *
     * @return string
     */
    public function signout(array $usr_ids, array $crs_obj_ids) : string
    {
        $result = [];

        foreach ($crs_obj_ids as $crs_obj_id) {
            $crs = $this->getCourses()[$crs_obj_id];

            $result[$crs->getId()] = [];

            foreach ($usr_ids as $usr_id) {
                if ($crs->getMembersObject()->isAssigned($usr_id)) {
                    $crs->getMembersObject()->delete($usr_id);

                    $result[$crs->getId()][] = $usr_id;
                }
            }
        }

        $result2 = [];
        foreach ($result as $crs_obj_id => $usr_ids2) {
            $result2[self::dic()->objDataCache()->lookupTitle($crs_obj_id)] = self::output()->getHTML(self::dic()->ui()->factory()->listing()->unordered(array_map(function (int $usr_id) : string {
                return ilObjUser::_lookupLogin($usr_id);
            }, $usr_ids2)));
        }

        if (!empty(array_filter($result2))) {
            return self::output()->getHTML([self::plugin()->translate("signedout", CourseAdministrationStaffGUI::LANG_MODULE), self::dic()->ui()->factory()->listing()->descriptive($result2)]);
        } else {
            return "";
        }
    }


    /**
     * @param int $crs_obj_id
     * @param int $usr_id
     *
     * @return CourseAdministrationEnrollment|null
     */
    public function getEnrollment(int $crs_obj_id, int $usr_id)/*:?CourseAdministrationEnrollment*/
    {
        /**
         * @var CourseAdministrationEnrollment|null $enrollment
         */

        $enrollment = CourseAdministrationEnrollment::where([
            "crs_obj_id" => $crs_obj_id,
            "usr_id"     => $usr_id
        ])->first();

        return $enrollment;
    }


    /**
     * @param int      $crs_obj_id
     * @param int      $usr_id
     * @param int|null $time
     *
     * @return CourseAdministrationEnrollment
     */
    public function setEnrolmentTime(int $crs_obj_id, int $usr_id, /*?*/ int $time = null) : CourseAdministrationEnrollment
    {
        $enrollment = $this->getEnrollment($crs_obj_id, $usr_id);

        if ($enrollment === null) {
            $enrollment = new CourseAdministrationEnrollment();

            $enrollment->setCrsObjId($crs_obj_id);

            $enrollment->setUsrId($usr_id);
        }

        $enrollment->setEnrollmentTime($time);

        $enrollment->setSignedoutTime(null);

        $enrollment->store();

        if (empty($enrollment->getId())) {
            $enrollment->setId(self::dic()->database()->getLastInsertId());
        }

        return $enrollment;
    }


    /**
     * @param int      $crs_obj_id
     * @param int      $usr_id
     * @param int|null $time
     *
     * @return CourseAdministrationEnrollment
     */
    public function setSignoutDate(int $crs_obj_id, int $usr_id, /*?*/ int $time = null) : CourseAdministrationEnrollment
    {
        $enrollment = $this->getEnrollment($crs_obj_id, $usr_id);

        if ($enrollment === null) {
            $enrollment = $this->setEnrolmentTime($crs_obj_id, $usr_id);
        }

        $enrollment->setEnrollmentTime(null);

        $enrollment->setSignedoutTime($time);

        $enrollment->store();

        return $enrollment;
    }


    /**
     * @param int $usr_id
     *
     * @return int|null
     */
    public function getChangedTime(int $usr_id)/*:?int*/
    {
        /**
         * @var CourseAdministrationEnrollment|null $enrollment
         */

        $changed_time = null;

        foreach (CourseAdministrationEnrollment::where(["usr_id" => $usr_id])->get() as $enrollment
        ) {
            if (!empty($enrollment->getEnrollmentTime()) && (empty($changed_time) || $enrollment->getEnrollmentTime() > $changed_time)) {
                $changed_time = $enrollment->getEnrollmentTime();
            }

            if (!empty($enrollment->getSignedoutTime()) && (empty($changed_time) || $enrollment->getSignedoutTime() > $changed_time)) {
                $changed_time = $enrollment->getSignedoutTime();
            }
        }

        return $changed_time;
    }
}

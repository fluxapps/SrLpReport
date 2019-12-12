<?php

namespace srag\Plugins\SrLpReport\Staff\CourseAdministration;

use ilAdvancedSelectionListGUI;
use ilCheckboxInputGUI;
use ilDatePresentation;
use ilDateTime;
use ilDateTimeInputGUI;
use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilObjCourse;
use ilOrgUnitPathStorage;
use ilTextInputGUI;
use ilUserSearchOptions;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchInputGUI\MultiSelectSearchInputGUI;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;

/**
 * Class CourseAdministrationTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff\CourseAdministration
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CourseAdministrationTableGUI extends AbstractStaffTableGUI
{

    const LANG_MODULE = CourseAdministrationStaffGUI::LANG_MODULE;
    /**
     * @inheritDoc
     */
    protected $actions = false;


    /**
     * @inheritDoc
     */
    protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch (true) {
            case $column === "org_units":
                $column = ilOrgUnitPathStorage::getTextRepresentationOfUsersOrgUnits($row["usr_id"]);
                break;

            case strpos($column, "crs_") === 0:
                /**
                 * @var ilObjCourse $crs
                 */
                $crs = $row[$column];

                if ($crs->getMembersObject()->isAssigned($row["usr_id"])) {
                    $column = [];

                    $status = intval(ilLPStatus::_lookupStatus($crs->getId(), $row["usr_id"]));
                    $img = ilLearningProgressBaseGUI::_getImagePathForStatus($status);
                    $text = ilLearningProgressBaseGUI::_getStatusText($status);

                    $column[] = self::output()->getHTML([
                        self::dic()->ui()->factory()->icon()->custom($img, $text),
                        self::dic()->ui()->factory()->legacy($text)
                    ]);

                    $column[] = "<br>";

                    $enrollment = self::ilias()->staff()->courseAdministration()->getEnrollment($crs->getId(), $row["usr_id"]);
                    if ($enrollment !== null) {
                        $enrollment_time = ilDatePresentation::formatDate(new ilDateTime($enrollment->getEnrollmentTime(), IL_CAL_UNIX));

                        $red = false;
                        if (empty($status)) {
                            if ((time() - $enrollment->getEnrollmentTime()) > (60 * 60 * 24 * Config::getField(Config::KEY_COURSE_ADMINISTRATION_MARK))) {
                                $red = true;
                            }
                        }
                    } else {
                        $enrollment_time = $this->txt("unknown");
                        $red = false;
                    }

                    $column[] = self::plugin()->translate("enrolled_date", self::LANG_MODULE, [$enrollment_time]);

                    $column = implode($column);

                    if ($red) {
                        $column = '<div class="alert-danger">' . $column . '</div>';
                    }
                } else {
                    self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, $row["usr_id"]);
                    self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_COURSE_OBJ_ID, $crs->getId());
                    $column = self::output()->getHTML(self::dic()
                        ->ui()
                        ->factory()
                        ->button()
                        ->standard($this->txt("enroll"), self::dic()->ctrl()->getLinkTarget($this->parent_obj, CourseAdministrationStaffGUI::CMD_ENROLL)));
                    self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, null);
                    self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_COURSE_OBJ_ID, null);
                }
                break;

            default:
                $column = $row[$column];
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = self::ilias()->staff()->users()->getColumns();

        foreach (self::ilias()->staff()->courseAdministration()->getCourses() as $crs_obj_id => $crs) {
            $columns["crs_" . $crs_obj_id] = [
                "default" => true,
                "sort"    => false,
                "txt"     => $crs->getTitle()
            ];
        }

        $no_sort = [
            "org_units",
            "interests_general",
            "interests_help_offered",
            "interests_help_looking",
            "learning_progress_courses"
        ];

        foreach ($columns as $id => &$column) {
            $column["id"] = $id;
            $column["default"] = ($column["default"] === true);
            if (!isset($column["sort"])) {
                $column["sort"] = (!in_array($id, $no_sort));
            }
        }

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function initColumns()/*: void*/
    {
        $this->addColumn("");

        parent::initColumns();
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->setSelectAllCheckbox(Reports::GET_PARAM_USR_ID);
        $this->addMultiCommand(CourseAdministrationStaffGUI::CMD_MULTI_ENROLL_SELECT, $this->txt("enroll"));
    }


    /**
     * @inheritDoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSorting(false);
        $this->setExternalSegmentation(false);

        $this->setDefaultOrderField("lastname");
        $this->setDefaultOrderDirection("asc");

        $this->determineLimit();
        $this->determineOffsetAndOrder();

        $data = self::ilias()->staff()->courseAdministration()->getData(self::dic()->user()->getId(), $this->getFilterValues2(), $this->getOrderField(), $this->getOrderDirection(), $this->getOffset(),
            $this->getLimit());

        $this->setMaxCount($data["max_count"]);
        $this->setData($data["data"]);
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [
            "user"                     => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => $this->dic()->language()->txt("login") . "/" . $this->dic()->language()->txt("email") . "/" . $this->dic()->language()
                        ->txt("name")
            ],
            "org_units"                 => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => self::ilias()->staff()->users()
                    ->getOrgUnits(),
                PropertyFormGUI::PROPERTY_NOT_ADD => (!ilUserSearchOptions::_isEnabled("org_units")),
                "setTitle"                        => $this->dic()->language()->txt("obj_orgu")
            ],
            "org_units_subsequent"                 => [
                PropertyFormGUI::PROPERTY_CLASS   => ilCheckboxInputGUI::class,
                PropertyFormGUI::PROPERTY_NOT_ADD => (!ilUserSearchOptions::_isEnabled("org_units")),
                "setTitle"                        => $this->dic()->language()->txt("obj_orgu") . " ".$this->txt("subsequent")
            ],
            "enrolled_crs_obj_ids"     => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => array_map(function (ilObjCourse $crs) : string {
                    return $crs->getTitle();
                }, self::ilias()->staff()->courseAdministration()->getCourses())
            ],
            "enrolled_lp_status"       => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                    ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM => self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED),
                    ilLPStatus::LP_STATUS_IN_PROGRESS_NUM   => self::dic()->language()->txt(ilLPStatus::LP_STATUS_IN_PROGRESS),
                    ilLPStatus::LP_STATUS_COMPLETED_NUM     => self::dic()->language()->txt(ilLPStatus::LP_STATUS_COMPLETED)
                    //ilLPStatus::LP_STATUS_FAILED_NUM => self::dic()->language()->txt(ilLPStatus::LP_STATUS_FAILED)
                ],
                "setTitle"                        => self::dic()->language()->txt("trac_learning_progress")
            ],
            "enrolled_before"          => [
                PropertyFormGUI::PROPERTY_CLASS => ilDateTimeInputGUI::class
            ],
            "not_enrolled_crs_obj_ids" => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => array_map(function (ilObjCourse $crs) : string {
                    return $crs->getTitle();
                }, self::ilias()->staff()->courseAdministration()->getCourses())
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId("srlprep_staff_crs_admin");
        $this->setPrefix("srlprep_staff_crs_admin");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("title"));
    }


    /**
     * @inheritDoc
     */
    protected function fillRow(/*array*/ $row)/*: void*/
    {
        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariable("CHECKBOX_POST_VAR", Reports::GET_PARAM_USR_ID);
        $this->tpl->setVariable("ID", $row["usr_id"]);
        $this->tpl->parseCurrentBlock();

        parent::fillRow($row);
    }


    /**
     * @inheritDoc
     */
    protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function getRightHTML() : string
    {
        return "";
    }
}

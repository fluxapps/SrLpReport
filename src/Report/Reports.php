<?php

namespace srag\Plugins\SrLpReport\Report;

use ilSrLpReportPlugin;
use ilTrQuery;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Reports
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Reports
{

    use SrLpReportTrait;
    use DICTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_USR_ID = "usr_id";
    const GET_PARAM_ORG_UNIT_ID = "org_unit_id";
    const GET_PARAM_COURSE_OBJ_ID = "course_obj_id";
    const GET_PARAM_TARGET = "target";
    const GET_PARAM_RETURN = "return";
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
     * Reports constructor
     */
    private function __construct()
    {

    }


    /**
     * @return int|null
     */
    public function getReportObjRefId()/*: ?int*/
    {
        $obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

        if ($obj_ref_id === null) {
            $param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

            $obj_ref_id = explode("_", $param_target)[1];
        }

        $obj_ref_id = intval($obj_ref_id);

        if ($obj_ref_id > 0) {
            return $obj_ref_id;
        } else {
            return null;
        }
    }


    /**
     * @return int|null
     */
    public function getUsrId()/*: ?int*/
    {
        $usr_id = filter_input(INPUT_GET, self::GET_PARAM_USR_ID);

        $usr_id = intval($usr_id);

        if ($usr_id > 0) {
            return $usr_id;
        } else {
            return null;
        }
    }


    /**
     * @param int        $ref_id
     * @param array|null $user_ids
     *
     * @return array
     */
    public function getChilds(int $ref_id,/*?*/ array $user_ids = null) : array
    {
        return array_unique(array_merge(
            array_values(ilTrQuery::getObjectIds(self::dic()->objDataCache()->lookupObjId($ref_id), $ref_id, true, !empty($user_ids), $user_ids)["ref_ids"]),
            (!empty($always_show_types = Config::getField(Config::KEY_REPORTING_ALWAYS_SHOW_CHILD_TYPES)) ? array_map(function (array $child) : int {
                return $child["child"];
            }, self::dic()->database()->fetchAll(self::dic()->database()->query(self::dic()->tree()->getSubTreeQuery($ref_id, [], $always_show_types)))) : [])
        ));
    }
}

<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\ActiveRecordConfig\SrLpReport\ActiveRecordConfigGUI;
use srag\Plugins\SrLpReport\Config\ConfigFormGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ilSrLpReportConfigGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLpReportConfigGUI extends ActiveRecordConfigGUI
{

    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const CMD_GET_COURSES_AUTO_COMPLETE = "getCoursesAutoComplete";
    /**
     * @var array
     */
    protected static $tabs = [self::TAB_CONFIGURATION => ConfigFormGUI::class];
    /**
     * @var array
     */
    protected static $custom_commands
        = [
            self::CMD_GET_COURSES_AUTO_COMPLETE
        ];


    /**
     *
     */
    protected function getCoursesAutoComplete()/*:void*/
    {
        $search = strval(filter_input(INPUT_GET, "term"));

        $options = [];

        foreach (self::ilias()->searchCourses($search) as $id => $title) {
            $options[] = [
                "id"   => $id,
                "text" => $title
            ];
        }

        self::output()->outputJSON(["results" => $options]);
    }
}

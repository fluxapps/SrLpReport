<?php

namespace srag\Plugins\SrLpReport\Staff\CourseAdministration;

use ilObjCourse;
use ilObjUser;
use ilSrLpReportPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrLpReport\HiddenInputGUI\HiddenInputGUI;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class CourseAdministrationMultiEnrollSelectFormGUI
 *
 * @package srag\Plugins\SrLpReport\Staff\CourseAdministratio
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CourseAdministrationMultiEnrollSelectFormGUI extends PropertyFormGUI
{

    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const LANG_MODULE = CourseAdministrationStaffGUI::LANG_MODULE;
    /**
     * @var int[]
     */
    protected $usr_ids = [];
    /**
     * @var int[]
     */
    protected $crs_obj_ids = [];


    /**
     * @inheritDoc
     *
     * @param int[] $usr_ids
     */
    public function __construct(CourseAdministrationStaffGUI $parent, array $usr_ids = [])
    {
        $this->usr_ids = $usr_ids;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "usr_ids":
                return json_encode($this->usr_ids);

            default:
                return $this->{$key};
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(CourseAdministrationStaffGUI::CMD_MULTI_ENROLL, $this->txt("enroll"));
        $this->addCommandButton(CourseAdministrationStaffGUI::CMD_INDEX, self::dic()->language()->txt("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        ilUtil::sendInfo(self::output()->getHTML([
            self::dic()->language()->txt("users"),
            self::dic()->ui()->factory()->listing()->unordered(array_map(function (int $usr_id) : string {
                return ilObjUser::_lookupLogin($usr_id);
            }, $this->usr_ids))
        ]));

        $this->fields = [
            "usr_ids"     => [
                self::PROPERTY_CLASS => HiddenInputGUI::class
            ],
            "crs_obj_ids" => [
                self::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                self::PROPERTY_OPTIONS => array_map(function (ilObjCourse $crs) : string {
                    return $crs->getTitle();
                }, self::ilias()->staff()->courseAdministration()->getCourses()),
                "setTitle"             => self::dic()->language()->txt("courses")
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case "usr_ids":
                $this->usr_ids = json_decode($value);
                break;

            default:
                $this->{$key} = $value;
                break;
        }
    }


    /**
     * @return int[]
     */
    public function getUsrIds() : array
    {
        return $this->usr_ids;
    }


    /**
     * @return int[]
     */
    public function getCrsObjIds() : array
    {
        return $this->crs_obj_ids;
    }
}

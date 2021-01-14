<?php

namespace srag\Plugins\SrLpReport\Staff\CourseAdministration;

use ilAdvancedSelectionListGUI;
use ilCheckboxInputGUI;
use ilDatePresentation;
use ilDateTime;
use ilDateTimeInputGUI;
use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilMStListUser as ilMStListUser54;
use ILIAS\MyStaff\ListUsers\ilMStListUser;
use ilObjCourse;
use ilOrgUnitPathStorage;
use ilTextInputGUI;
use ilUserDefinedFields;
use ilUserSearchOptions;
use srag\CustomInputGUIs\SrLpReport\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\OrgUnitAjaxAutoCompleteCtrl;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrLpReport\TabsInputGUI\MultilangualTabsInputGUI;
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
     *
     * @param ilMStListUser|ilMStListUser54 $row
     */
    protected function getColumnValue(string $column, /*ilMStListUser*/ $row, int $format = self::DEFAULT_FORMAT) : string
    {
        if (self::version()->is6()) {
            $icon_factory = self::dic()->ui()->factory()->symbol()->icon();
        } else {
            $icon_factory = self::dic()->ui()->factory()->icon();
        }

        switch (true) {
            case $column === "org_units":
                $column = ilOrgUnitPathStorage::getTextRepresentationOfUsersOrgUnits($row->getUsrId());
                break;

            case strpos($column, "udf_field_") === 0:
                $column = $row->{$column};
                break;

            case $column === "user_language":
                $column = MultilangualTabsInputGUI::getLanguages()[$row->user_language];
                break;

            case strpos($column, "crs_") === 0:
                /**
                 * @var ilObjCourse $crs
                 */
                $crs = $row->{$column};

                $enrollment = self::ilias()->staff()->courseAdministration()->getEnrollment($crs->getId(), $row->getUsrId());
                $enrolled = $crs->getMembersObject()->isAssigned($row->getUsrId());

                $column = [];
                $red = false;

                self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, $row->getUsrId());
                self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_COURSE_OBJ_ID, $crs->getId());
                if ($enrolled) {
                    $column[] = self::output()->getHTML(self::dic()
                        ->ui()
                        ->factory()
                        ->button()
                        ->standard($this->txt("signout"), self::dic()->ctrl()->getLinkTarget($this->parent_obj, CourseAdministrationStaffGUI::CMD_SIGNOUT)));
                } else {
                    $column[] = self::output()->getHTML(self::dic()
                        ->ui()
                        ->factory()
                        ->button()
                        ->standard($this->txt("enroll"), self::dic()->ctrl()->getLinkTarget($this->parent_obj, CourseAdministrationStaffGUI::CMD_ENROLL)));
                }
                self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, null);
                self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_COURSE_OBJ_ID, null);

                if ($enrolled) {
                    if ($enrollment !== null) {
                        if (!empty($enrollment->getEnrollmentTime())) {
                            $enrollment_time = ilDatePresentation::formatDate(new ilDateTime($enrollment->getEnrollmentTime(), IL_CAL_UNIX));

                            if (empty($status)) {
                                if ((time() - $enrollment->getEnrollmentTime()) > (60 * 60 * 24 * Config::getField(Config::KEY_COURSE_ADMINISTRATION_MARK))) {
                                    $red = true;
                                }
                            }
                        } else {
                            $enrollment_time = $this->txt("unknown");
                        }
                    } else {
                        $enrollment_time = $this->txt("unknown");
                    }
                    $column[] = "<br>";
                    $column[] = "<br>";
                    $column[] = self::plugin()->translate("enrolled_date", self::LANG_MODULE, [$enrollment_time]);

                    $status = intval(ilLPStatus::_lookupStatus($crs->getId(), $row->getUsrId()));
                    $img = ilLearningProgressBaseGUI::_getImagePathForStatus($status);
                    $text = ilLearningProgressBaseGUI::_getStatusText($status);

                    $column[] = "<br>";
                    $column[] = self::output()->getHTML([
                        $icon_factory->custom($img, $text),
                        self::dic()->ui()->factory()->legacy($text)
                    ]);
                } else {
                    if ($enrollment !== null) {
                        if (!empty($enrollment->getSignedoutTime())) {
                            $signedout_time = ilDatePresentation::formatDate(new ilDateTime($enrollment->getSignedoutTime(), IL_CAL_UNIX));
                        } else {
                            $signedout_time = $this->txt("unknown");
                        }
                    } else {
                        //$signedout_time = $this->txt("unknown");
                        $signedout_time = "";
                    }

                    if (!empty($signedout_time)) {
                        $column[] = "<br>";
                        $column[] = "<br>";
                        $column[] = self::plugin()->translate("signedout_time", self::LANG_MODULE, [$signedout_time]);
                    }
                }

                $column = implode($column);

                if ($red) {
                    $column = '<div class="alert-danger">' . $column . '</div>';
                }
                break;


            case $column === "changed_time":
                if (!empty($row->changed_time)) {
                    $column = ilDatePresentation::formatDate(new ilDateTime($row->changed_time, IL_CAL_UNIX));
                } else {
                    $column = $this->txt("unknown");
                }
                break;

            default:
                $column = Items::getter($row, $column);
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = self::ilias()->staff()->users()->getColumns();

            foreach (array_filter(ilUserDefinedFields::_getInstance()->getDefinitions(), function (array $field) : bool {
                    return in_array($field["field_id"], Config::getField(Config::KEY_COURSE_ADMINISTRATION_UDF_FIELDS));
                }) as $field) {
                $columns["udf_field_" . $field["field_id"]] = [
                    "default" => true,
                    "sort"    => false,
                    "txt"     => $field["field_name"]
                ];
            }

            $columns["user_language"] = [
                "default" => true,
                "txt"     => $this->dic()->language()->txt("user") . " " . $this->dic()->language()->txt("language")
            ];

            foreach (self::ilias()->staff()->courseAdministration()->getCourses() as $crs_obj_id => $crs) {
                $columns["crs_" . $crs_obj_id] = [
                    "default" => true,
                    "sort"    => false,
                    "txt"     => $crs->getTitle()
                ];
            }

            $columns["changed_time"] = [
                "default" => true
            ];

            $no_sort = [
                "org_units",
                "interests_general",
                "interests_help_offered",
                "interests_help_looking",
                "learning_progress_courses",
                "user_language",
                "changed_time"
            ];

            foreach ($columns as $id => &$column) {
                $column["id"] = $id;
                $column["default"] = ($column["default"] === true);
                if (!isset($column["sort"])) {
                    $column["sort"] = (!in_array($id, $no_sort));
                }
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
        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);

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
        $this->setFilterCols(2);

        $this->filter_fields = [
            "user"                     => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => $this->dic()->language()->txt("login") . "/" . $this->dic()->language()->txt("email") . "/" . $this->dic()->language()
                        ->txt("name")
            ],
            "org_units"                 => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
               "setAjaxAutoCompleteCtrl" => new OrgUnitAjaxAutoCompleteCtrl(),
                PropertyFormGUI::PROPERTY_NOT_ADD => (!ilUserSearchOptions::_isEnabled("org_units")),
                "setTitle"                        => $this->dic()->language()->txt("obj_orgu")
            ],
            "org_units_subsequent"                 => [
                PropertyFormGUI::PROPERTY_CLASS   => ilCheckboxInputGUI::class,
                PropertyFormGUI::PROPERTY_NOT_ADD => (!ilUserSearchOptions::_isEnabled("org_units")),
                "setTitle"                        => $this->dic()->language()->txt("obj_orgu") . " ".$this->txt("subsequent")
            ]] + array_reduce(array_filter(ilUserDefinedFields::_getInstance()->getDefinitions(), function (array $field) : bool {
        return in_array($field["field_id"], Config::getField(Config::KEY_COURSE_ADMINISTRATION_UDF_FIELDS));
    }), function (array $filter, array $field) : array {
                $filter["udf_field_" . $field["field_id"]] = [
                    PropertyFormGUI::PROPERTY_CLASS => MultiLineNewInputGUI::class,
                    PropertyFormGUI::PROPERTY_SUBITEMS => [
                      "value" => [
                          PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
                      ]
                    ],
                    "setShowSort" => false,
                    "setShowInputLabel" => MultiLineNewInputGUI::SHOW_INPUT_LABEL_NONE,
                    "setTitle"                      => $field["field_name"]
                ];

                return $filter;
            }, []) + [
            "enrolled_crs_obj_ids"     => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => array_map(function (ilObjCourse $crs) : string {
                    return $crs->getTitle();
                }, self::ilias()->staff()->courseAdministration()->getCourses())
            ],
            "enrolled_lp_status"       => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
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
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => array_map(function (ilObjCourse $crs) : string {
                    return $crs->getTitle();
                }, self::ilias()->staff()->courseAdministration()->getCourses())
            ],
            "user_language"            => [
                PropertyFormGUI::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => MultilangualTabsInputGUI::getLanguages(),
                "setTitle"                        => $this->dic()->language()->txt("user") . " " . $this->dic()->language()->txt("language")
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
     *
     * @param ilMStListUser|ilMStListUser54 $row
     */
    protected function fillRow(/*ilMStListUser*/ $row)/*: void*/
    {
        $this->tpl->setCurrentBlock("checkbox");
        $this->tpl->setVariable("CHECKBOX_POST_VAR", Reports::GET_PARAM_USR_ID);
        $this->tpl->setVariable("ID", $row->getUsrId());
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

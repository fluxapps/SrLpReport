<?php

namespace srag\Plugins\SrLpReport\Config;

use ilCheckboxInputGUI;
use ilNumberInputGUI;
use ilSrLpReportConfigGUI;
use ilSrLpReportPlugin;
use ilSrLpReportUIHookGUI;
use srag\ActiveRecordConfig\SrLpReport\ActiveRecordConfigFormGUI;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\MultiSelectSearchNewInputGUI;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\ObjectsAjaxAutoCompleteCtrl;
use srag\Plugins\SrLpReport\Staff\CourseAdministration\CourseAdministrationStaffGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ConfigFormGUI
 *
 * @package srag\Plugins\SrLpReport\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ConfigFormGUI extends ActiveRecordConfigFormGUI {

	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const CONFIG_CLASS_NAME = Config::class;


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		self::dic()->language()->loadLanguageModule("trac");
		self::dic()->language()->loadLanguageModule("notes");

		$this->fields = [
            Config::KEY_REPORTING_ALWAYS_SHOW_CHILD_TYPES => [
                self::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                self::PROPERTY_OPTIONS => array_combine(ilSrLpReportUIHookGUI::TYPES, array_map(function (string $type) : string {
                    return self::dic()->language()->txt("objs_" . $type);
                }, ilSrLpReportUIHookGUI::TYPES)),
                "setTitle"             => self::plugin()->translate(Config::KEY_REPORTING_ALWAYS_SHOW_CHILD_TYPES, self::LANG_MODULE, [self::dic()->language()->txt("learning_progress")])
            ],
            Config::KEY_SHOW_MATRIX_ACTIONS => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate(Config::KEY_SHOW_MATRIX_ACTIONS, self::LANG_MODULE, [self::dic()->language()->txt("trac_matrix")]),
                ],
            Config::KEY_SHOW_ONLY_APPEARABLE_ORG_UNITS_IN_FILTER => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            Config::KEY_ENABLE_COURSES_VIEW => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("enable_view", self::LANG_MODULE, [self::dic()->language()->txt("courses")])
            ],
            Config::KEY_ENABLE_REPORTING_VIEW => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    Config::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT => [
                        self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                        self::PROPERTY_SUBITEMS => [
                            Config::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT_NEW_OBJECTS => [
                                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                                "setTitle"           => self::plugin()->translate("reporting_per_object_new_objects", self::LANG_MODULE)
                            ]
                        ],
                        "setTitle"              => self::plugin()->translate("reporting_per_object", self::LANG_MODULE)
                    ]
                ],
                "setTitle"              => self::plugin()->translate("reporting", self::LANG_MODULE)
            ],
            Config::KEY_ENABLE_USERS_VIEW   => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("enable_view", self::LANG_MODULE, [self::dic()->language()->txt("users")])
            ],
            Config::KEY_ENABLE_COURSE_ADMINISTRATION => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                "setTitle"              => self::plugin()->translate("enable_view", self::LANG_MODULE, [self::plugin()->translate("title", CourseAdministrationStaffGUI::LANG_MODULE)]),
                self::PROPERTY_SUBITEMS => [
                    Config::KEY_COURSE_ADMINISTRATION_COURSES => [
                        self::PROPERTY_CLASS   => MultiSelectSearchNewInputGUI::class,
                        "setAjaxAutoCompleteCtrl"          => new ObjectsAjaxAutoCompleteCtrl(ilSrLpReportUIHookGUI::TYPE_CRS)
                    ],
                    Config::KEY_COURSE_ADMINISTRATION_MARK    => [
                        self::PROPERTY_CLASS => ilNumberInputGUI::class,
                        "setSuffix"          => $this->txt(CourseAdministrationStaffGUI::LANG_MODULE . "_mark_days")
                    ]
                ]
            ],
            Config::KEY_ENABLE_COMMENTS => [
            self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
            "setTitle" => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("notes_comments")
        ],
            Config::KEY_SYNC_POSITION_PERMISSIONS_WITH_CHILDREN => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ]
		];
	}
}

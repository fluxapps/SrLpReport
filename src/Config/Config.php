<?php

namespace srag\Plugins\SrLpReport\Config;

use ilSrLpReportPlugin;
use srag\ActiveRecordConfig\SrLpReport\ActiveRecordConfig;
use srag\Plugins\SrLpReport\Report\Matrix\MatrixReportGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Config
 *
 * @package srag\Plugins\SrLpReport\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Config extends ActiveRecordConfig {

	use SrLpReportTrait;
	const TABLE_NAME = "ui_uihk_srcrslp_config";
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const KEY_COURSE_ADMINISTRATION_COURSES = "course_administration_courses";
    const KEY_COURSE_ADMINISTRATION_MARK = "course_administration_mark";
    const KEY_COURSE_ADMINISTRATION_UDF_FIELDS = "course_administration_udf_fields";
	const KEY_ENABLE_COMMENTS = "enable_comments";
    const KEY_ENABLE_COURSES_VIEW = "enable_course_view";
    const KEY_ENABLE_COURSE_ADMINISTRATION = "enable_course_administration";
    const KEY_ENABLE_REPORTING_VIEW = "enable_reporting_view";
    const KEY_REPORTING_DEFAULT_VIEW = "reporting_default_view";
    const KEY_ENABLE_REPORTING_VIEW_PER_OBJECT = "enable_reporting_view_per_object";
    const KEY_ENABLE_REPORTING_VIEW_PER_OBJECT_NEW_OBJECTS = "enable_reporting_view_per_object_new_objects";
    const KEY_ENABLE_USERS_VIEW = "enable_users_view";
    const KEY_REPORTING_ALWAYS_SHOW_CHILD_TYPES = "reporting_always_show_child_types";
    const KEY_SHOW_MATRIX_ACTIONS = "show_matrix_actions";
    const KEY_SYNC_POSITION_PERMISSIONS_WITH_CHILDREN = "sync_position_permissions_with_children";
	/**
	 * @var array
	 */
	protected static $fields = [
        self::KEY_COURSE_ADMINISTRATION_COURSES => [ self::TYPE_JSON, [] ],
        self::KEY_COURSE_ADMINISTRATION_MARK => [ self::TYPE_INTEGER, 10 ],
        self::KEY_COURSE_ADMINISTRATION_UDF_FIELDS => [ self::TYPE_JSON, [] ],
		self::KEY_ENABLE_COMMENTS => [ self::TYPE_BOOLEAN, false ],
        self::KEY_ENABLE_COURSES_VIEW => [ self::TYPE_BOOLEAN, false ],
        self::KEY_ENABLE_COURSE_ADMINISTRATION => [ self::TYPE_BOOLEAN, false ],
        self::KEY_ENABLE_REPORTING_VIEW => [ self::TYPE_BOOLEAN, true ],
        self::KEY_REPORTING_DEFAULT_VIEW => [self::TYPE_STRING, MatrixReportGUI::TAB_ID ],
        self::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT => [ self::TYPE_BOOLEAN, false ],
        self::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT_NEW_OBJECTS => [ self::TYPE_BOOLEAN, false ],
        self::KEY_ENABLE_USERS_VIEW => [ self::TYPE_BOOLEAN, false ],
        self::KEY_REPORTING_ALWAYS_SHOW_CHILD_TYPES => [ self::TYPE_JSON, [] ],
        self::KEY_SHOW_MATRIX_ACTIONS => [ self::TYPE_BOOLEAN, false ],
        self::KEY_SYNC_POSITION_PERMISSIONS_WITH_CHILDREN => [self::TYPE_BOOLEAN, false ]
	];
}

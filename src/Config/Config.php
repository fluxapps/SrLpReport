<?php

namespace srag\Plugins\SrLpReport\Config;

use ilSrLpReportPlugin;
use srag\ActiveRecordConfig\SrLpReport\ActiveRecordConfig;
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
	const KEY_ENABLE_COMMENTS = "enable_comments";
	/**
	 * @var array
	 */
	protected static $fields = [
		self::KEY_ENABLE_COMMENTS => [ self::TYPE_BOOLEAN, false ]
	];
}

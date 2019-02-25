<?php

namespace srag\Plugins\SrLpReport\Config;

use ilSrLpReportPlugin;
use srag\ActiveRecordConfig\SrLpReport\ActiveRecordConfigFormGUI;
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
		$this->fields = [];
	}
}

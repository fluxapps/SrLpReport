<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class StaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\StaffGUI: ilUIPluginRouterGUI
 */
class StaffGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const CMD_LIST = "list";


	/**
	 * StaffGUI constructor
	 */
	public function __construct() {

	}

	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				if (!self::access()->hasReportingAccess()) {
					ilUtil::sendInfo(self::plugin()->translate("no_reporting_access"), true);
					self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
				}

				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_LIST:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}
}

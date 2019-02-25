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
	const CMD_APPLY_FILTER = "applyFilter";
	const CMD_STAFF = "staff";
	const CMD_RESET_FILTER = "resetFilter";
	const LANG_MODULE_STAFF = "staff";


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
				if (!self::access()->hasStaffAccess()) {
					ilUtil::sendFailure(self::dic()->language()->txt("permission_denied"), true);

					self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
				}

				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_APPLY_FILTER:
					case self::CMD_RESET_FILTER:
					case self::CMD_STAFF:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 * @param string $cmd
	 *
	 * @return StaffTableGUI
	 */
	protected function getStaffTable(string $cmd = self::CMD_STAFF): StaffTableGUI {
		$table = new StaffTableGUI($this, $cmd);

		return $table;
	}


	/**
	 *
	 */
	protected function staff()/*: void*/ {
		$table = $this->getStaffTable();

		self::output()->output($table, true);
	}


	/**
	 *
	 */
	protected function applyFilter()/*: void*/ {
		$table = $this->getStaffTable(self::CMD_APPLY_FILTER);

		$table->writeFilterToSession();

		self::dic()->ctrl()->redirect($this, self::CMD_STAFF);
	}


	/**
	 *
	 */
	protected function resetFilter()/*: void*/ {
		$table = $this->getStaffTable(self::CMD_RESET_FILTER);

		$table->resetFilter();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this, self::CMD_STAFF);
	}
}

<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractStaffGUI
 *
 * @package srag\Plugins\SrLpReport\Staff
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractStaffGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const CMD_INDEX = "index";
	const CMD_APPLY_FILTER = "applyFilter";
	const CMD_RESET_FILTER = "resetFilter";
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const TAB_ID = "";


	/**
	 * AbstractStaffGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		self::dic()->tabs()->activateTab(static::TAB_ID);

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_INDEX:
					case self::CMD_APPLY_FILTER:
					case self::CMD_RESET_FILTER:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 *
	 */
	protected function index()/*: void*/ {
		$table = $this->getTable();

		self::output()->output($table, true);
	}


	/**
	 *
	 */
	protected function applyFilter()/*: void*/ {
		$table = $this->getTable(self::CMD_APPLY_FILTER);

		$table->writeFilterToSession();

		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 *
	 */
	protected function resetFilter()/*: void*/ {
		$table = $this->getTable(self::CMD_RESET_FILTER);

		$table->resetFilter();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 * @param string $cmd
	 *
	 * @return AbstractStaffTableGUI
	 */
	protected abstract function getTable(string $cmd = self::CMD_INDEX): AbstractStaffTableGUI;
}

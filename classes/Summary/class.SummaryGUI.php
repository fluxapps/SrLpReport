<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/docs/LICENSE */

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\SrCrsLpReport\Utils\SrCrsLpReportTrait;
use srag\DIC\SrCrsLpReport\DICTrait;

/**
 * Class SummaryGUI
 *
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy SummaryGUI: ilUIPluginRouterGUI
 */
class SummaryGUI {

	use DICTrait;
	use SrCrsLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrCrsLpReportPlugin::class;
	const TAB_ID = "srcrslpsummary";
	const CMD_EDIT = "edit";
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_INDEX = 'index';
	const CMD_RESET_FILTER = 'resetFilter';


	/**
	 * @var \HookilTrObjectUsersPropsTableGUI
	 */
	protected $table;


	/**
	 * SummaryGUI constructor
	 */
	public function __construct() {
		self::tabgui()->setTabs();

		$this->initJS();
	}

	/**
	 *
	 */
	protected function initJS()/*: void*/ {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/node_modules/d3/dist/d3.min.js");
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/js/d3.legend.js");
	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {

		self::dic()->ctrl()->saveParameter($this,'ref_id');
		self::dic()->ctrl()->saveParameter($this,'details_id');


		$cmd = self::dic()->ctrl()->getCmd();
		switch ($cmd) {
			case self::CMD_RESET_FILTER:
			case self::CMD_APPLY_FILTER:
			case self::CMD_INDEX:
				$this->$cmd();
				break;
			default:
				$this->index();
				break;
		}

	}




	public function index() {
		$this->listRecords();
	}


	public function listRecords() {
		$this->table = new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());

		self::output()->output($this->table->getHTML(), true);
	}


	public function applyFilter() {
		$this->table = new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());
		$this->table->writeFilterToSession();
		$this->table->resetOffset();
		self::dic()->ctrl()->redirect($this);
	}


	public function resetFilter() {
		$this->table = new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());
		$this->table->resetOffset();
		$this->table->resetFilter();
		self::dic()->ctrl()->redirect($this);
	}

}

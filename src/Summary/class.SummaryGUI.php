<?php

namespace srag\Plugins\SrLpReport\Summary;

use ilObject;
use ilSrLpReportPlugin;
use ilTemplateException;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class SummaryGUI
 *
 * @package           srag\Plugins\SrLpReport\Summary
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Summary\SummaryGUI: srag\Plugins\SrLpReport\GUI\BaseGUI
 */
class SummaryGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const TAB_ID = "srcrslpsummary";
	const CMD_EDIT = "edit";
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_INDEX = 'index';
	const CMD_RESET_FILTER = 'resetFilter';
	/**
	 * @var SummaryGUI
	 */
	protected $table;


	/**
	 * SummaryGUI constructor
	 */
	public function __construct() {
		self::tabgui()->setTabs();

		$this->initJS();

		$type = self::dic()->objDataCache()->lookupType(ilObject::_lookupObjectId($_GET['ref_id']));
		$icon = ilObject::_getIcon("", "tiny", $type);

		self::dic()->mainTemplate()->setTitleIcon($icon);

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("learning_progress") . " "
			. ilObject::_lookupTitle(ilObject::_lookupObjectId($_GET['ref_id'])));
	}


	/**
	 *
	 */
	protected function initJS()/*: void*/ {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/vendor/srag/custominputguis/node_modules/d3/dist/d3.min.js");
	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		self::dic()->ctrl()->saveParameter($this, 'ref_id');
		self::dic()->ctrl()->saveParameter($this, 'details_id');

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


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function index() {
		$this->listRecords();
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function listRecords() {
		$this->table = new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());

		self::output()->output($this->getTableAndFooterHtml(), true);
	}


	/**
	 *
	 */
	public function applyFilter() {
		$this->table = new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());
		$this->table->writeFilterToSession();
		$this->table->resetOffset();
		self::dic()->ctrl()->redirect($this);
	}


	/**
	 *
	 */
	public function resetFilter() {
		$this->table = new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());
		$this->table->resetOffset();
		$this->table->resetFilter();
		self::dic()->ctrl()->redirect($this);
	}


	/**
	 * @return string
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function getTableAndFooterHtml(): string {
		$tpl = self::plugin()->template("Report/report.html", false, false);
		$tpl->setVariable("REPORT", self::output()->getHTML($this->table));
		$tpl->setVariable('LEGEND', BaseGUI::getLegendHTML());

		return self::output()->getHTML($tpl);
	}
}

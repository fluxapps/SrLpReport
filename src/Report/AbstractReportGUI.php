<?php

namespace srag\Plugins\SrLpReport\Report;

use ilObject;
use ilSrLpReportPlugin;
use ilTemplateException;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractReportGUI
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractReportGUI {

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
	 * AbstractReportGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function executeCommand()/*: void*/ {
		self::dic()->tabs()->activateSubTab(static::TAB_ID);

		$this->initGUI();

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				$cmd = self::dic()->ctrl()->getCmd(self::CMD_INDEX);

				switch ($cmd) {
					case self::CMD_INDEX:
					case self::CMD_APPLY_FILTER:
					case self::CMD_RESET_FILTER:
						$this->{$cmd}();
						break;

					default:
						break;
				}
		}
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function initGUI()/*: void*/ {
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . "/css/srcrsreport.css");

		$type = self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId()));

		$icon = ilObject::_getIcon("", "tiny", $type);

		self::dic()->mainTemplate()->setTitleIcon($icon);

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("learning_progress") . " " . self::dic()->objDataCache()
				->lookupTitle(self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId())));
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function index()/*: void*/ {
		self::output()->output($this->getTableAndFooterHtml(), true);
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function applyFilter()/*: void*/ {
		$table = $this->getTable(self::CMD_APPLY_FILTER);

		$table->writeFilterToSession();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this);
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function resetFilter()/*: void*/ {
		$table = $this->getTable(self::CMD_RESET_FILTER);

		$table->resetOffset();

		$table->resetFilter();

		self::dic()->ctrl()->redirect($this);
	}


	/**
	 * @return string
	 *
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function getTableAndFooterHtml(): string {
		$tpl = self::plugin()->template("Report/report.html", false, false);

		$tpl->setVariable("REPORT", self::output()->getHTML($this->getTable()));

		$tpl->setVariable('LEGEND', ReportGUI::getLegendHTML());

		return self::output()->getHTML($tpl);
	}


	/**
	 * @param string $cmd
	 *
	 * @return AbstractReportTableGUI
	 */
	protected abstract function getTable(string $cmd = self::CMD_INDEX): AbstractReportTableGUI;
}

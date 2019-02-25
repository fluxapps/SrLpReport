<?php

namespace srag\Plugins\SrLpReport\GUI;

use ilLink;
use ilObject;
use ilSrLpReportPlugin;
use ilTemplateException;
use ilUtil;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\Report\ReportFactory;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractGUI
 *
 * @package srag\Plugins\SrLpReport\GUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractGUI {

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
	 * AbstractGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function executeCommand()/*: void*/ {
		if (!$this->hasAccess()) {
			ilUtil::sendFailure(self::dic()->language()->txt("permission_denied"), true);

			self::dic()->ctrl()->redirectToURL(ilLink::_getLink(ReportFactory::getReportObjRefId()));
		}

		$this->initGUI();

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


	/**
	 * @return bool
	 */
	public function hasAccess(): bool {
		return self::access()->hasLPReadAccess(ReportFactory::getReportObjRefId());
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function initGUI()/*: void*/ {
		self::tabgui()->setTabs();

		self::dic()->ctrl()->saveParameter($this, ReportFactory::GET_PARAM_REF_ID);

		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . "/css/srcrsreport.css");

		$type = self::dic()->objDataCache()->lookupType(ilObject::_lookupObjectId(ReportFactory::getReportObjRefId()));

		$icon = ilObject::_getIcon("", "tiny", $type);

		self::dic()->mainTemplate()->setTitleIcon($icon);

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("learning_progress") . " "
			. ilObject::_lookupTitle(ilObject::_lookupObjectId(ReportFactory::getReportObjRefId())));
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
		$table = $this->getTable();

		$table->writeFilterToSession();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this);
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected function resetFilter()/*: void*/ {
		$table = $this->getTable();

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

		$tpl->setVariable('LEGEND', BaseGUI::getLegendHTML());

		return self::output()->getHTML($tpl);
	}


	/**
	 * @return TableGUI
	 *
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	protected abstract function getTable(): TableGUI;
}

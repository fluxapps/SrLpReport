<?php

namespace srag\Plugins\SrLpReport\Report;

use ilPanelGUI;
use ilSrLpReportPlugin;
use ilUtil;
use srag\CustomInputGUIs\SrLpReport\CustomInputGUIsTrait;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractReportTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractReportTableGUI extends TableGUI {

	use SrLpReportTrait;
	use CustomInputGUIsTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * @inheritdoc
	 */
	protected function initExport()/*: void*/ {
		$this->setExportFormats([ self::EXPORT_EXCEL, self::EXPORT_CSV ]);
	}


	/**
	 * @return string
	 */
	protected function getRightHTML(): string {
		$tpl = self::plugin()->template("LearningProgress/legend.html", false, false);

		$tpl->setVariable("IMG_NOT_ATTEMPTED", ilUtil::getImagePath("scorm/not_attempted.svg"));
		$tpl->setVariable("IMG_IN_PROGRESS", ilUtil::getImagePath("scorm/incomplete.svg"));
		$tpl->setVariable("IMG_COMPLETED", ilUtil::getImagePath("scorm/completed.svg"));

		$tpl->setVariable("TXT_NOT_ATTEMPTED", self::dic()->language()->txt("trac_not_attempted"));
		$tpl->setVariable("TXT_IN_PROGRESS", self::dic()->language()->txt("trac_in_progress"));
		$tpl->setVariable("TXT_COMPLETED", self::dic()->language()->txt("trac_completed"));

		$panel = ilPanelGUI::getInstance();
		$panel->setPanelStyle(ilPanelGUI::PANEL_STYLE_PRIMARY);
		$panel->setBody(self::output()->getHTML($tpl));

		return self::output()->getHTML($panel);
	}


	/**
	 * @return string
	 */
	public function getHTML(): string {
		self::dic()->mainTemplate()->setRightContent($this->getRightHTML());

		return parent::getHTML();
	}
}

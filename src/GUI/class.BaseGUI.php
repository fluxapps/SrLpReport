<?php

namespace srag\Plugins\SrLpReport\GUI;

use ilPanelGUI;
use ilSrLpReportPlugin;
use ilTemplateException;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\Matrix\MatrixGUI;
use srag\Plugins\SrLpReport\Summary\SummaryGUI;
use srag\Plugins\SrLpReport\User\UserGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class BaseGUI
 *
 * @package           srag\Plugins\SrLpReport\GUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\GUI\BaseGUI: ilUIPluginRouterGUI
 */
class BaseGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * BaseGUI constructor
	 */
	public function __construct() {
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . "/css/srcrsreport.css");
	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			case strtolower(UserGUI::class):
				self::dic()->ctrl()->forwardCommand(new UserGUI());
				break;
			case strtolower(MatrixGUI::class):
				self::dic()->ctrl()->forwardCommand(new MatrixGUI());
				break;
			case strtolower(SummaryGUI::class):
				self::dic()->ctrl()->forwardCommand(new SummaryGUI());
				break;
			default:
				break;
		}
	}


	/**
	 * @return string
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public static function getLegendHTML(): string {
		$tpl = self::plugin()->template("LearningProgress/legend.html", false, false);

		$tpl->setVariable("IMG_NOT_ATTEMPTED", ilUtil::getImagePath("scorm/not_attempted.svg"));
		$tpl->setVariable("IMG_IN_PROGRESS", ilUtil::getImagePath("scorm/incomplete.svg"));
		$tpl->setVariable("IMG_COMPLETED", ilUtil::getImagePath("scorm/completed.svg"));

		$tpl->setVariable("TXT_NOT_ATTEMPTED", self::dic()->language()->txt("trac_not_attempted"));
		$tpl->setVariable("TXT_IN_PROGRESS", self::dic()->language()->txt("trac_in_progress"));
		$tpl->setVariable("TXT_COMPLETED", self::dic()->language()->txt("trac_completed"));

		$panel = ilPanelGUI::getInstance();
		$panel->setPanelStyle(ilPanelGUI::PANEL_STYLE_PRIMARY);
		$panel->setBody($tpl->get());

		return self::output()->getHTML($panel->getHTML());
	}
}

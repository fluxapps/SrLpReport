<?php

require_once __DIR__ . "/../vendor/autoload.php";



use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use srag\DIC\SrLpReport\DICTrait;


/**
 * Class ilSrLpReportGUI
 *
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 *
 * @ilCtrl_isCalledBy ilSrLpReportGUI: ilUIPluginRouterGUI
 */
class ilSrLpReportGUI {
	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * ilSrLpReportGUI constructor.
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
			default:
				$report = self::report()->buildReportByClassName($next_class);
				self::dic()->ctrl()->forwardCommand($report->getGuiObject());
				break;
		}

	}

	public static function getLegendHTML()
	{

		$tpl = self::plugin()->template("LearningProgress/legend.html",false, false);


		$tpl->setVariable("IMG_NOT_ATTEMPTED",
			ilUtil::getImagePath("scorm/not_attempted.svg"));
		$tpl->setVariable("IMG_IN_PROGRESS",
			ilUtil::getImagePath("scorm/incomplete.svg"));
		$tpl->setVariable("IMG_COMPLETED",
			ilUtil::getImagePath("scorm/completed.svg"));

		$tpl->setVariable("TXT_NOT_ATTEMPTED",
			self::dic()->language()->txt("trac_not_attempted"));
		$tpl->setVariable("TXT_IN_PROGRESS",
			self::dic()->language()->txt("trac_in_progress"));
		$tpl->setVariable("TXT_COMPLETED",
			self::dic()->language()->txt("trac_completed"));



		$panel = ilPanelGUI::getInstance();
		$panel->setPanelStyle(ilPanelGUI::PANEL_STYLE_PRIMARY);
		$panel->setBody($tpl->get());

		return $panel->getHTML();
	}
}
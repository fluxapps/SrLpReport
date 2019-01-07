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
		global $lng;

		$tpl = new ilTemplate("tpl.lp_legend.html", true, true, "Services/Tracking");
		$tpl->setVariable("IMG_NOT_ATTEMPTED",
			ilUtil::getImagePath("scorm/not_attempted.svg"));
		$tpl->setVariable("IMG_IN_PROGRESS",
			ilUtil::getImagePath("scorm/incomplete.svg"));
		$tpl->setVariable("IMG_COMPLETED",
			ilUtil::getImagePath("scorm/completed.svg"));
		$tpl->setVariable("IMG_FAILED",
			ilUtil::getImagePath("scorm/failed.svg"));
		$tpl->setVariable("TXT_NOT_ATTEMPTED",
			$lng->txt("trac_not_attempted"));
		$tpl->setVariable("TXT_IN_PROGRESS",
			$lng->txt("trac_in_progress"));
		$tpl->setVariable("TXT_COMPLETED",
			$lng->txt("trac_completed"));
		$tpl->setVariable("TXT_FAILED",
			$lng->txt("trac_failed"));

		$panel = ilPanelGUI::getInstance();
		$panel->setPanelStyle(ilPanelGUI::PANEL_STYLE_SECONDARY);
		$panel->setBody($tpl->get());

		return $panel->getHTML();
	}
}
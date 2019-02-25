<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\Matrix\MatrixGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;
use srag\Plugins\SrLpReport\Summary\SummaryGUI;
use srag\Plugins\SrLpReport\User\UserGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ilSrLpReportUIHookGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLpReportUIHookGUI extends ilUIHookPluginGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const PAR_TABS = "tabs";
	const REDIRECT = "redirect";
	/**
	 * @var bool[]
	 */
	protected static $load = [
		self::REDIRECT => false
	];


	/**
	 * ilSrLpReportUIHookGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param string $a_comp
	 * @param string $a_part
	 * @param array  $a_par
	 */
	public function modifyGUI(/*string*/
		$a_comp, /*string*/
		$a_part, /*array*/
		$a_par = [])/*: void*/ {
		if (!self::$load[self::REDIRECT]) {

			if ($a_part === self::PAR_TABS) {

				if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLPListOfObjectsGUI::class)) {

					self::$load[self::REDIRECT] = true;

					switch (self::dic()->ctrl()->getCmd()) {
						case "showUserObjectMatrix":
							self::dic()->ctrl()
								->setParameterByClass(MatrixGUI::class, ReportFactory::GET_PARAM_REF_ID, ReportFactory::getReportObjRefId());

							self::dic()->ctrl()->redirectByClass(array( ilUIPluginRouterGUI::class, BaseGUI::class, MatrixGUI::class ));
							break;

						case "showObjectSummary":
							self::dic()->ctrl()
								->setParameterByClass(SummaryGUI::class, ReportFactory::GET_PARAM_REF_ID, ReportFactory::getReportObjRefId());

							self::dic()->ctrl()->redirectByClass(array( ilUIPluginRouterGUI::class, BaseGUI::class, SummaryGUI::class ));
							break;

						case "":
							self::dic()->ctrl()
								->setParameterByClass(UserGUI::class, ReportFactory::GET_PARAM_REF_ID, ReportFactory::getReportObjRefId());

							self::dic()->ctrl()->redirectByClass(array( ilUIPluginRouterGUI::class, BaseGUI::class, UserGUI::class ));
							break;

						default:
							break;
					}
				}
			}
		}
	}
}

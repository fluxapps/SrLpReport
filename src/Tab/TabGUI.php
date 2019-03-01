<?php

namespace srag\Plugins\SrLpReport\Tab;

use ilLearningProgressGUI;
use ilLink;
use ilLPListOfSettingsGUI;
use ilObjCourseGUI;
use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\GUI\AbstractGUI;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\Matrix\MatrixGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;
use srag\Plugins\SrLpReport\Summary\SummaryGUI;
use srag\Plugins\SrLpReport\User\UserGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class TabGUI
 *
 * @package srag\Plugins\SrLpReport\Tabs
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class TabGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const TAB_LEARNING_PROGRESS = "learning_progress";
	const TAB_SETTINGS = "trac_settings";
	/**
	 * @var AbstractGUI[]
	 */
	const TAB_GUI_CLASSES = [ UserGUI::class, MatrixGUI::class, SummaryGUI::class ];
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * TabGUI constructor
	 */
	private function __construct() {

	}


	/**
	 * @throws DICException
	 */
	public function setTabs()/*: void*/ {
		self::dic()->language()->loadLanguageModule("trac");

		self::dic()->ctrl()->saveParameterByClass(ilLearningProgressGUI::class, ReportFactory::GET_PARAM_REF_ID);

		self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("course"), ilLink::_getLink(ReportFactory::getReportObjRefId()));

		self::dic()->tabs()->addTab(self::TAB_LEARNING_PROGRESS, self::dic()->language()->txt("learning_progress"), self::dic()->ctrl()
			->getLinkTargetByClass([
				ilRepositoryGUI::class,
				ilObjCourseGUI::class,
				ilLearningProgressGUI::class
			]));
		self::dic()->tabs()->activateTab(self::TAB_LEARNING_PROGRESS);

		foreach (self::TAB_GUI_CLASSES as $tab_gui) {

			/**
			 * @var AbstractGUI $gui
			 */
			$gui = new $tab_gui();

			if ($gui->hasAccess()) {

				self::dic()->ctrl()->saveParameterByClass($tab_gui, ReportFactory::GET_PARAM_REF_ID);

				self::dic()->tabs()->addSubTabTarget($tab_gui::TAB_ID, self::dic()->ctrl()->getLinkTargetByClass([
					ilUIPluginRouterGUI::class,
					BaseGUI::class,
					$tab_gui
				]));

				// Get unchanged cmdClass get parameter (ilCtrl has a bug and removes Slashes)
				if (filter_input(INPUT_GET, "cmdClass") === strtolower($tab_gui)) {
					self::dic()->tabs()->activateSubTab($tab_gui::TAB_ID);
				}
			}
		}

		if (self::access()->hasLPWriteAccess(ReportFactory::getReportObjRefId())) {
			self::dic()->tabs()->addSubTabTarget(self::TAB_SETTINGS, self::dic()->ctrl()->getLinkTargetByClass([
				ilRepositoryGUI::class,
				ilObjCourseGUI::class,
				ilLearningProgressGUI::class,
				ilLPListOfSettingsGUI::class
			]));
		}
	}
}

<?php

namespace srag\Plugins\SrCrsLpReport\Tab;

use srag\Plugins\SrCrsLpReport\Utils\SrCrsLpReportTrait;
use ilSrCrsLpReportPlugin;
use srag\DIC\SrCrsLpReport\DICTrait;
use SrCrsLpReportGUI;
use MatrixGUI;
use SummaryGUI;

/**
 * Class TabGUI
 *
 *
 * @package srag\Plugins\SrCrsLpReport\Tabs
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class TabGUI {

	use DICTrait;
	use SrCrsLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrCrsLpReportPlugin::class;

	const TAB_GUI_CLASSES = array('SrCrsLpReportGUI','MatrixGUI','SummaryGUI');
	const CLASS_PLUGIN_ROUTER_GUI = 'ilUIPluginRouterGUI';

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
	 * Access constructor
	 */
	private function __construct() {

	}


	public function setTabs()/*: void*/ {
		self::dic()->tabs()->clearTargets();

		foreach (self::TAB_GUI_CLASSES as $tab_gui) {

			self::dic()->ctrl()->saveParameterByClass($tab_gui,'ref_id');
			self::dic()->ctrl()->saveParameterByClass($tab_gui,'details_id');


			self::dic()->tabs()->addTab($tab_gui::TAB_ID, self::plugin()->translate($tab_gui::TAB_ID), self::dic()->ctrl()
				->getLinkTargetByClass([
					self::CLASS_PLUGIN_ROUTER_GUI,
					$tab_gui
				]));

			if(self::dic()->ctrl()->getCmdClass() == strtolower($tab_gui)) {
				self::dic()->tabs()->activateTab($tab_gui::TAB_ID);
			}
		}


	}
}

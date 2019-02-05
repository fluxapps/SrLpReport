<?php

namespace srag\Plugins\SrLpReport\Tab;

use ilSrLpReportPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\Matrix\MatrixGUI;
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
	const TAB_GUI_CLASSES = array( UserGUI::class, MatrixGUI::class, SummaryGUI::class );
	const CLASS_PLUGIN_BASE_GUI = BaseGUI::class;
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
		self::dic()->tabs()->clearTargets();

		foreach (self::TAB_GUI_CLASSES as $tab_gui) {

			self::dic()->ctrl()->saveParameterByClass($tab_gui, 'ref_id');
			self::dic()->ctrl()->saveParameterByClass($tab_gui, 'details_id');

			self::dic()->ctrl()->setParameterByClass($tab_gui, 'sr_rp', 1);

			self::dic()->tabs()->addTab($tab_gui::TAB_ID, self::plugin()->translate($tab_gui::TAB_ID), self::dic()->ctrl()->getLinkTargetByClass([
				ilUIPluginRouterGUI::class,
				self::CLASS_PLUGIN_BASE_GUI,
				$tab_gui
			]));

			// Get unchanged cmdClass (ilCtrl has a bug and removes Slashes)
			if (filter_input(INPUT_GET, "cmdClass") == strtolower($tab_gui)) {
				self::dic()->tabs()->activateTab($tab_gui::TAB_ID);
			}
		}
	}
}

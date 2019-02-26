<?php

namespace srag\Plugins\SrLpReport\Menu;

use ILIAS\GlobalScreen\Provider\StaticProvider\AbstractStaticPluginMainMenuProvider;
use ilSrLpReportPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Menu
 *
 * @package srag\Plugins\SrLpReport\Menu
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class Menu extends AbstractStaticPluginMainMenuProvider {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getStaticTopItems(): array {
		return [
			self::dic()->globalScreen()->mainmenu()->topLinkItem(self::dic()->globalScreen()->identification()->plugin(self::plugin()
				->getPluginObject(), $this)->identifier(ilSrLpReportPlugin::PLUGIN_ID))->withTitle(self::dic()->language()->txt("my_staff"))
				->withAction(self::dic()->ctrl()->getLinkTargetByClass([ ilUIPluginRouterGUI::class, StaffGUI::class ], StaffGUI::CMD_STAFF))
				->withAvailableCallable(function (): bool {
					return self::plugin()->getPluginObject()->isActive();
				})->withVisibilityCallable(function (): bool {
					return self::access()->hasStaffAccess();
				})
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getStaticSubItems(): array {
		return [];
	}
}

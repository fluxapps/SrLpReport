<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\GlobalScreen\Provider\StaticProvider\AbstractStaticPluginMainMenuProvider;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl\ctrlmmEntryCtrl;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;
use srag\Plugins\SrLpReport\Access\Access;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Menu\Menu;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use srag\RemovePluginDataConfirm\SrLpReport\PluginUninstallTrait;

/**
 * Class ilSrLpReportPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLpReportPlugin extends ilUserInterfaceHookPlugin {

	use PluginUninstallTrait;
	use SrLpReportTrait;
	const PLUGIN_ID = "srlprep";
	const PLUGIN_NAME = "SrLpReport";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = SrLpReportRemoveDataConfirm::class;
	/**
	 * @var self|null
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
	 * ilSrLpReportPlugin constructor
	 */
	public function __construct() {
		parent::__construct();
	}


	/**
	 * @return string
	 */
	public function getPluginName(): string {
		return self::PLUGIN_NAME;
	}


	/**
	 * @inheritdoc
	 */
	public function promoteGlobalScreenProvider(): AbstractStaticPluginMainMenuProvider {
		return new Menu(self::dic()->dic(), $this);
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(Config::TABLE_NAME, false);

		if (!self::version()->is54()) {
			$this->removeCtrlMainMenu();
		}
	}


	/**
	 *
	 */
	protected function afterActivation()/*: void*/ {
		if (!self::version()->is54()) {
			$this->addCtrlMainMenu();
		}
	}


	/**
	 *
	 */
	protected function afterDeactivation()/*: void*/ {
		if (!self::version()->is54()) {
			$this->removeCtrlMainMenu();
		}
	}


	/**
	 *
	 */
	protected function addCtrlMainMenu()/*: void*/ {
		try {
			include_once __DIR__ . "/../../CtrlMainMenu/vendor/autoload.php";

			if (class_exists(ctrlmmEntry::class)) {
				if (count(ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", StaffGUI::class))) === 0) {
					$entry = new ctrlmmEntryCtrl();
					$entry->setTitle(self::PLUGIN_NAME);
					$entry->setTranslations([
						"en" => self::plugin()->translate("my_staff", "", [], false, "en"),
						"de" => self::plugin()->translate("my_staff", "", [], false, "de")
					]);
					$entry->setGuiClass(implode(",", [ ilUIPluginRouterGUI::class, StaffGUI::class ]));
					$entry->setCmd(StaffGUI::CMD_STAFF);
					$entry->setPermissionType(ctrlmmMenu::PERM_SCRIPT);
					$entry->setPermission(json_encode([
						__DIR__ . "/../vendor/autoload.php",
						Access::class,
						"hasStaffAccess"
					]));
					$entry->store();
				}
			}
		} catch (Throwable $ex) {
		}
	}


	/**
	 *
	 */
	protected function removeCtrlMainMenu()/*: void*/ {
		try {
			include_once __DIR__ . "/../../CtrlMainMenu/vendor/autoload.php";

			if (class_exists(ctrlmmEntry::class)) {
				foreach (ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", StaffGUI::class)) as $entry) {
					/**
					 * @var ctrlmmEntry $entry
					 */
					$entry->delete();
				}
			}
		} catch (Throwable $ex) {
		}
	}
}

<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Staff\Courses\CoursesStaffGUI;
use srag\Plugins\SrLpReport\Staff\User\UserStaffGUI;
use srag\Plugins\SrLpReport\Staff\Users\UsersStaffGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class StaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\StaffGUI: ilUIPluginRouterGUI
 */
class StaffGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * StaffGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		if (!self::access()->hasStaffAccess()) {
			ilUtil::sendFailure(self::dic()->language()->txt("permission_denied"), true);

			self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
		}

		$this->setTabs();

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			case strtolower(UsersStaffGUI::class):
				self::dic()->ctrl()->forwardCommand(new UsersStaffGUI());
				break;
			case strtolower(UserStaffGUI::class):
				self::dic()->ctrl()->forwardCommand(new UserStaffGUI());
				break;
			case strtolower(CoursesStaffGUI::class):
				self::dic()->ctrl()->forwardCommand(new CoursesStaffGUI());
				break;
			default:
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs()/*: void*/ {
		self::dic()->language()->loadLanguageModule("mst");
		self::dic()->language()->loadLanguageModule("trac");

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("my_staff"));

		self::dic()->tabs()->addTab(UsersStaffGUI::TAB_ID, self::dic()->language()->txt("users"), self::dic()->ctrl()
			->getLinkTargetByClass(UsersStaffGUI::class));

		self::dic()->tabs()->addTab(CoursesStaffGUI::TAB_ID, self::dic()->language()->txt("courses"), self::dic()->ctrl()
			->getLinkTargetByClass(CoursesStaffGUI::class));
	}
}

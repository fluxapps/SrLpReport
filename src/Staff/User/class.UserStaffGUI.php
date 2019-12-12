<?php

namespace srag\Plugins\SrLpReport\Staff\User;

use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilMStListCourse;
use ilMyStaffAccess;
use ilUIPluginRouterGUI;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Staff\Users\UsersStaffGUI;

/**
 * Class UserStaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff\User
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\User\UserStaffGUI: srag\Plugins\SrLpReport\Staff\StaffGUI
 */
class UserStaffGUI extends AbstractStaffGUI {

	const TAB_ID = "user";
    const ENABLE_CONFIG_KEY = Config::KEY_ENABLE_USERS_VIEW;


	/**
	 * @inheritdoc
	 */
	protected function setTabs()/*: void*/ {
		self::dic()->ctrl()->saveParameter($this, Reports::GET_PARAM_USR_ID);

		self::dic()->tabs()->clearTargets();

		self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()->getLinkTargetByClass([
			ilUIPluginRouterGUI::class,
			StaffGUI::class,
			UsersStaffGUI::class
		]));
	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractStaffTableGUI {
		$table = new UserTableGUI($this, $cmd);

		return $table;
	}


	/**
	 * @inheritdoc
	 */
	protected function getActionsArray(): array {
		return self::ilias()->staff()->user()->getActionsArray();
	}
}

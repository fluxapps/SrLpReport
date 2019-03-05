<?php

namespace srag\Plugins\SrLpReport\Staff\Users;

use ilAdvancedSelectionListGUI;
use ilUIPluginRouterGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Staff\User\UserTableGUI;

/**
 * Class UserStaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff\Users
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\Users\UserStaffGUI: srag\Plugins\SrLpReport\Staff\StaffGUI
 */
class UserStaffGUI extends AbstractStaffGUI {

	const TAB_ID = "user";


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

		self::dic()->tabs()->addTab(UserStaffGUI::TAB_ID, self::dic()->language()->txt("user"), self::dic()->ctrl()
			->getLinkTargetByClass(UserStaffGUI::class));
		self::dic()->tabs()->activateTab(UserStaffGUI::TAB_ID);
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
	protected function fillActions(ilAdvancedSelectionListGUI $actions)/*: void*/ {
		self::ilias()->staff()->user()->fillActions($actions);
	}
}

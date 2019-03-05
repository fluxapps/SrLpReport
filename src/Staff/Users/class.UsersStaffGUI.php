<?php

namespace srag\Plugins\SrLpReport\Staff\Users;

use ilAdvancedSelectionListGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;

/**
 * Class UsersStaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff\Users
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\Users\UsersStaffGUI: srag\Plugins\SrLpReport\Staff\StaffGUI
 */
class UsersStaffGUI extends AbstractStaffGUI {

	const TAB_ID = "users";


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractStaffTableGUI {
		$table = new UsersTableGUI($this, $cmd);

		return $table;
	}


	/**
	 * @inheritdoc
	 */
	protected function getActions()/*: void*/ {
		$actions = new ilAdvancedSelectionListGUI();

		self::ilias()->staff()->users()->fillActions($actions);

		self::output()->output($actions->getHTML(true));
	}
}

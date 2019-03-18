<?php

namespace srag\Plugins\SrLpReport\Report\User;

use srag\Plugins\SrLpReport\Report\AbstractReport2TableGUI;

/**
 * Class UserTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserTableGUI extends AbstractReport2TableGUI {

	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId('srcrslp_usrs');
		$this->setPrefix('srcrslp_usrs');
	}
}

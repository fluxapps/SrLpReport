<?php

namespace srag\Plugins\SrLpReport\User;

use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;

/**
 * Class UserTableGUI
 *
 * @package srag\Plugins\SrLpReport\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserTableGUI extends AbstractReportTableGUI {

	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId('srcrslp_usrs');
		$this->setPrefix('srcrslp_usrs');
	}


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false): string {
		switch ($column) {
			default:
				break;
		}

		return parent::getColumnValue($column, /*array*/
			$row, /*bool*/
			$raw_export);
	}
}

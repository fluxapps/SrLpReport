<?php

namespace srag\Plugins\SrLpReport\Summary;

use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\GUI\AbstractGUI;

/**
 * Class SummaryGUI
 *
 * @package           srag\Plugins\SrLpReport\Summary
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Summary\SummaryGUI: srag\Plugins\SrLpReport\GUI\BaseGUI
 */
class SummaryGUI extends AbstractGUI {

	const TAB_ID = "trac_summary";


	/**
	 *
	 */
	protected function initJS()/*: void*/ {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/vendor/srag/custominputguis/node_modules/d3/dist/d3.min.js");
	}


	/**
	 * @inheritdoc
	 */
	public function executeCommand()/*: void*/ {
		$this->initJS();

		parent::executeCommand();
	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(): TableGUI {
		return new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());
	}
}

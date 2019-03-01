<?php

namespace srag\Plugins\SrLpReport\Report\Summary;

use srag\Plugins\SrLpReport\Report\AbstractReportGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;

/**
 * Class SummaryReportGUI
 *
 * @package           srag\Plugins\SrLpReport\Report\Summary
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\Summary\SummaryReportGUI: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class SummaryReportGUI extends AbstractReportGUI {

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
	protected function getTable(): AbstractReportTableGUI {
		return new SummaryTableGUI($this, self::dic()->ctrl()->getCmd());
	}
}

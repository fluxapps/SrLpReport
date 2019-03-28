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
	 * @inheritdoc
	 */
	protected function setTabs()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractReportTableGUI {
		return new SummaryTableGUI($this, $cmd);
	}


	/**
	 * @inheritdoc
	 */
	protected function getActionsArray(): array {
		return [];
	}
}

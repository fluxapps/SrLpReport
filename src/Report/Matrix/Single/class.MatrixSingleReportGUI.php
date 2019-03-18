<?php

namespace srag\Plugins\SrLpReport\Report\Matrix\Single;

use srag\Plugins\SrLpReport\Report\AbstractReportGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;

/**
 * Class MatrixSingleReportGUI
 *
 * @package           srag\Plugins\SrLpReport\Report\Matrix\Single
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\Matrix\Single\MatrixSingleReportGUI: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class MatrixSingleReportGUI extends AbstractReportGUI {

	const TAB_ID = "trac_matrix_single";


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractReportTableGUI {
		return new MatrixSingleTableGUI($this, $cmd);
	}
}

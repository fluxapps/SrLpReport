<?php

namespace srag\Plugins\SrLpReport\Report\Matrix\Single;

use ilUIPluginRouterGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\Matrix\MatrixReportGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;
use srag\Plugins\SrLpReport\Report\Reports;

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
	protected function setTabs()/*: void*/ {
		self::dic()->ctrl()->saveParameter($this, Reports::GET_PARAM_USR_ID);

		self::dic()->tabs()->clearTargets();

		self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()->getLinkTargetByClass([
			ilUIPluginRouterGUI::class,
			ReportGUI::class,
			MatrixReportGUI::class
		]));
	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractReportTableGUI {
		return new MatrixSingleTableGUI($this, $cmd);
	}
}

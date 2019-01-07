<?php
require_once __DIR__ . "/../../../vendor/autoload.php";

/**
 * Class MatrixSingleObjectSingleUserGUI
 *
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy MatrixSingleObjectSingleUserGUI: ilSrLpReportGUI
 */
class MatrixSingleObjectSingleUserGUI extends AbstractMatrixGUI {

	function getTableGuiClassName(): string {
		return MatrixSingleObjectSingleUserTableGUI::class;
	}
}

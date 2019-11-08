<?php

namespace srag\Plugins\SrLpReport\Report;

use ilAdvancedSelectionListGUI;
use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilSrLpReportPlugin;
use srag\CustomInputGUIs\SrLpReport\CustomInputGUIsTrait;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractReportTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractReportTableGUI extends TableGUI {

	use SrLpReportTrait;
	use CustomInputGUIsTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * @inheritdoc
	 */
	protected function initExport()/*: void*/ {
		$this->setExportFormats([ self::EXPORT_EXCEL, self::EXPORT_CSV, self::EXPORT_PDF ]);
	}


	/**
	 * @return string
	 */
	public function getHTML(): string {
		self::dic()->mainTemplate()->setRightContent($this->getRightHTML());

		return parent::getHTML();
	}


	/**
	 * @return array
	 */
	public final function getFilterValues2(): array {
		$filter = $this->getFilterValues();

		if (isset($filter["status"])) {
			if ($filter["status"] > 0) {
				$filter["status"] -= 1;
			} else {
				unset($filter["status"]);
			}
		}

		if (isset($filter["lp_status"])) {
			if ($filter["lp_status"] > 0) {
				$filter["lp_status"] -= 1;
			} else {
				unset($filter["lp_status"]);
			}
		}

		return $filter;
	}


	/**
	 * @param ilAdvancedSelectionListGUI $actions
	 * @param array                      $row
	 */
	protected abstract function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/
	;


	/**
	 * @return string
	 */
	protected abstract function getRightHTML(): string;

	/**
	 * @param int $status
	 * @param int $percentage
	 *
	 * @return string
	 */
	protected function getLearningProgressRepresentation(int $status = 0, int $percentage = 0): string {
		switch ($status) {
			case 0:
				$path = ilLearningProgressBaseGUI::_getImagePathForStatus($status);
				$text = self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED);
				break;
			default:
				$path = ilLearningProgressBaseGUI::_getImagePathForStatus($status);
				$text = ilLearningProgressBaseGUI::_getStatusText($status);
				break;
		}

		if($status == ilLPStatus::LP_STATUS_COMPLETED_NUM && !$percentage) {
			$percentage = 100;
		}

		$representation = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($path, $text));
		if ($percentage > 0) {
			$representation = $representation . " " . $percentage . "%";
		}

		return $representation;
	}


	/**
	 * @param int $status
	 * @param int $percentage
	 *
	 * @return string
	 */
	protected function getLearningProgressRepresentationExport(int $status = 0, int $percentage = 0): string {

		if($status == ilLPStatus::LP_STATUS_COMPLETED_NUM && !$percentage) {
			$percentage = 100;
		}

		if ($percentage > 0) {
			return $percentage . "%";
		}

		switch ($status) {
			case 0:
				return self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED);
			default:
				return ilLearningProgressBaseGUI::_getStatusText($status);
		}
	}
}

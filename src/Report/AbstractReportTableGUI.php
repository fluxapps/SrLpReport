<?php

namespace srag\Plugins\SrLpReport\Report;

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
		$this->setExportFormats([ self::EXPORT_EXCEL, self::EXPORT_CSV ]);
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
	protected final function getFilterValues2(): array {
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
	 * @return string
	 */
	protected abstract function getRightHTML(): string;
}

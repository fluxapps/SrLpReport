<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilAdvancedSelectionListGUI;
use ilSrLpReportPlugin;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class StaffTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class StaffTableGUI extends TableGUI {

	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const LANG_MODULE = StaffGUI::LANG_MODULE_STAFF;


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/
		$column, /*array*/
		$row, /*bool*/
		$raw_export = false): string {
		switch ($column) {
			default:
				$column = $row[$column];
				break;
		}

		return strval($column);
	}


	/**
	 * @inheritdoc
	 */
	public function getSelectableColumns2(): array {
		$columns = [];

		return $columns;
	}

	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		parent::initColumns();

		$this->addColumn(self::plugin()->translate("actions", self::LANG_MODULE));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setData([]);
	}


	/**
	 * @inheritdoc
	 */
	public function initFilterFields()/*: void*/ {
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("srcrslp_staff");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate("staff", self::LANG_MODULE));
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		parent::fillRow($row);

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::plugin()->translate("actions", self::LANG_MODULE));
		$this->tpl->setVariable("COLUMN", self::output()->getHTML($actions));
		$this->tpl->parseCurrentBlock();
	}
}

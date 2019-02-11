<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilAdvancedSelectionListGUI;
use ilSelectInputGUI;
use ilSrLpReportPlugin;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
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
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);

		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$data = self::ilias()->staff()->getData(self::dic()->user()
			->getId(), $this->getFilterValues(), $this->getOrderField(), $this->getOrderDirection(), $this->getOffset(), $this->getLimit());

		$this->setMaxCount($data["max_count"]);
		$this->setData($data["data"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {
		self::dic()->language()->loadLanguageModule("mst");

		$this->filter_fields = [
			"user" => [
				PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
				"setTitle" => $this->dic()->language()->txt("login") . "/" . $this->dic()->language()->txt("email") . "/" . $this->dic()->language()
						->txt("name")
			],
			"org_unit" => [
				PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
				PropertyFormGUI::PROPERTY_OPTIONS => [ 0 => self::dic()->language()->txt("mst_opt_all") ] + self::ilias()->staff()->getOrgUnits(),
				"setTitle" => $this->dic()->language()->txt("obj_orgu"),
			]
		];
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
		$this->setTitle(self::dic()->language()->txt("my_staff"));
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		parent::fillRow($row);

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::dic()->language()->txt("actions"));
		$this->tpl->setVariable("COLUMN", self::output()->getHTML($actions));
		$this->tpl->parseCurrentBlock();
	}
}

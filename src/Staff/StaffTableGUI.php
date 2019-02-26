<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilAdvancedSelectionListGUI;
use ilSelectInputGUI;
use ilSrLpReportPlugin;
use ilTextInputGUI;
use ilUserSearchOptions;
use srag\CustomInputGUIs\SrLpReport\CustomInputGUIsTrait;
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
	use CustomInputGUIsTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/
		$column, /*array*/
		$row, /*bool*/
		$raw_export = false): string {
		switch ($column) {
			case "learning_progress_courses":
				if (!$raw_export) {
					$column = self::output()->getHTML(self::customInputGUIs()->learningProgressPie()->objIds()->withObjIds($row[$column])
						->withUsrId($row["usr_id"])->withId($row["usr_id"]));
				} else {
					$column = "";
				}
				break;

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
		$columns = self::ilias()->staff()->getColumns();

		$columns["learning_progress_courses"] = [
			"default" => true,
			"txt" => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("courses")
		];

		$no_sort = [
			"org_units",
			"interests_general",
			"interests_help_offered",
			"interests_help_looking",
			"learning_progress_courses"
		];

		foreach ($columns as $id => &$column) {
			$column["id"] = $id;
			$column["default"] = ($column["default"] === true);
			$column["sort"] = (!in_array($id, $no_sort));
		}

		return $columns;
	}


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		$this->addColumn("");

		parent::initColumns();

		$this->addColumn(self::dic()->language()->txt("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);

		$this->setDefaultOrderField("lastname");
		$this->setDefaultOrderDirection("asc");

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
	protected function initExport()/*: void*/ {
		$this->setExportFormats([ self::EXPORT_CSV, self::EXPORT_EXCEL ]);
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
				PropertyFormGUI::PROPERTY_NOT_ADD => (!ilUserSearchOptions::_isEnabled("org_units")),
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
		$this->tpl->setCurrentBlock("column");
		$this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->image()
			->standard($row["usr_obj"]->getPersonalPicturePath("small"), $row["usr_obj"]->getPublicName())));
		$this->tpl->parseCurrentBlock();

		parent::fillRow($row);

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::dic()->language()->txt("actions"));
		$this->tpl->setVariable("COLUMN", self::output()->getHTML($actions));
		$this->tpl->parseCurrentBlock();
	}
}

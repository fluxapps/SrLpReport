<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilAdvancedSelectionListGUI;
use ilSrLpReportPlugin;
use ilTextInputGUI;
use ilUIPluginRouterGUI;
use srag\CustomInputGUIs\SrLpReport\CustomInputGUIsTrait;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;
use srag\Plugins\SrLpReport\User\UserGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class CoursesTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CoursesTableGUI extends TableGUI {

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
			case "learning_progress_users":
				if (!$raw_export) {
					$column = self::output()->getHTML(self::customInputGUIs()->learningProgressPie()->usrIds()->withObjId($row["crs_obj_id"])
						->withUsrIds($row[$column])->withId($row["crs_ref_id"]));
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
		$columns = [
			"crs_title" => [
				"default" => true,
				"txt" => self::dic()->language()->txt("title"),
			],
			"learning_progress_users" => [
				"default" => true,
				"txt" => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("my_staff")
			]
		];

		$no_sort = [
			"learning_progress_users"
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
		parent::initColumns();

		$this->addColumn(self::dic()->language()->txt("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);

		$this->setDefaultOrderField("crs_title");
		$this->setDefaultOrderDirection("asc");

		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$data = self::ilias()->staff()->courses()
			->getData($this->getFilterValues(), $this->getOrderField(), $this->getOrderDirection(), $this->getOffset(), $this->getLimit());

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
		$this->setFilterCommand(StaffGUI::CMD_COURSES_APPLY_FILTER);
		$this->setResetCommand(StaffGUI::CMD_COURSES_RESET_FILTER);

		$this->filter_fields = [
			"crs_title" => [
				PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
				"setTitle" => $this->dic()->language()->txt("title")
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("srcrslp_courses");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::dic()->language()->txt("courses"));
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		parent::fillRow($row);

		self::dic()->ctrl()->setParameterByClass(UserGUI::class, ReportFactory::GET_PARAM_REF_ID, $row["crs_ref_id"]);

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::dic()->language()->txt("actions"));
		$actions->addItem(self::dic()->language()->txt("details"), "", self::dic()->ctrl()->getLinkTargetByClass([
			ilUIPluginRouterGUI::class,
			BaseGUI::class,
			UserGUI::class
		]));
		$this->tpl->setVariable("COLUMN", self::output()->getHTML($actions));
		$this->tpl->parseCurrentBlock();
	}
}

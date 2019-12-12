<?php

namespace srag\Plugins\SrLpReport\Staff\Courses;

use ilAdvancedSelectionListGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;

/**
 * Class CoursesTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff\Courses
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CoursesTableGUI extends AbstractStaffTableGUI {

	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT): string {
		switch (true) {
			case $column === "crs_title":
				$column = $row[$column];
				if (!$format) {
					$learning_progress_link = self::ilias()->staff()->courses()->getLearningProgressLink($row["crs_ref_id"]);
					if (!empty($learning_progress_link)) {
						$column = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($column, $learning_progress_link));
					}
				}
				break;

			case $column === "learning_progress_users":
				if (!$format) {
					$column = self::output()->getHTML($row["pie"]);
				} else {
                    $column = "";
				}
				break;

            case $column === "learning_progress_users_count":
                return $column = $row["pie"]->getData()["count"];

            case strpos($column, "learning_progress_users_") === 0:
                $status = intval(substr($column, strlen("learning_progress_users_")));

                $column = $row["pie"]->getData()["data"][$status]["value"];
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
				"txt" => self::dic()->language()->txt("title")
			]
		];

        if ($this->getExportMode()) {
            $columns["learning_progress_users_count"] = [
                "default" => true,
                "txt"     => self::dic()->language()->txt("total") . " " . self::dic()->language()->txt("users")
            ];
            foreach (self::customInputGUIs()->learningProgressPie()->usrIds()->getTitles() as $status => $title) {
                $columns["learning_progress_users_" . $status] = [
                    "default" => true,
                    "txt"     => $title
                ];
            }
        } else {
            $columns["learning_progress_users"] = [
                "default" => true,
                "txt" => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("users")
            ];
        }

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
		$this->setExternalSorting(false);
		$this->setExternalSegmentation(false);

		$this->setDefaultOrderField("crs_title");
		$this->setDefaultOrderDirection("asc");

		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$data = self::ilias()->staff()->courses()
			->getData($this->getFilterValues2(), $this->getOrderField(), $this->getOrderDirection(),0,0);

        $data["data"] = array_map(function (array $row) : array {
            $row["pie"] = self::customInputGUIs()->learningProgressPie()->usrIds()->withObjId($row["crs_obj_id"])->withUsrIds($row["learning_progress_users"]);

            if ($this->getExportMode()) {
                $row["pie"] = $row["pie"]->withShowEmpty(true);
            }

            return $row;
        }, $data["data"]);

		$this->setMaxCount($data["max_count"]);
		$this->setData($data["data"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {
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
		$this->setId("srlprep_staff_courses");
        $this->setPrefix("srlprep_staff_courses");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::dic()->language()->txt("courses"));
	}


	/**
	 * @inheritdoc
	 */
	protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/ {
		self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_REF_ID, $row["crs_ref_id"]);

		$actions->setId($row["crs_obj_id"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function getRightHTML(): string {
		return "";
	}
}

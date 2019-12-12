<?php

namespace srag\Plugins\SrLpReport\Staff\Users;

use ilAdvancedSelectionListGUI;
use ilOrgUnitPathStorage;
use ilSelectInputGUI;
use ilTextInputGUI;
use ilUserSearchOptions;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;

/**
 * Class UsersTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff\Users
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UsersTableGUI extends AbstractStaffTableGUI {

	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT): string {
		switch (true) {
			case $column === "login":
            case $column === "lastname":
				$column = $row[$column];
				if (!$format) {
					$column = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($column, self::ilias()->staff()->users()
						->getUserCoursesLink($row["usr_id"])));
				}
				break;

			case $column === "org_units":
				$column =  ilOrgUnitPathStorage::getTextRepresentationOfUsersOrgUnits($row["usr_id"]);
				break;

			case $column === "learning_progress_courses":
				if (!$format) {
					$column = self::output()->getHTML($row["pie"]);
				} else {
                    $column = "";
				}
				break;

            case $column === "learning_progress_courses_count":
                return $column = $row["pie"]->getData()["count"];

            case strpos($column, "learning_progress_courses_") === 0:
                $status = intval(substr($column, strlen("learning_progress_courses_")));

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
		$columns = self::ilias()->staff()->users()->getColumns();

        if ($this->getExportMode()) {
            $columns["learning_progress_courses_count"] = [
                "default" => true,
                "txt"     => self::dic()->language()->txt("total") . " " . self::dic()->language()->txt("courses")
            ];
            foreach (self::customInputGUIs()->learningProgressPie()->objIds()->getTitles() as $status => $title) {
                $columns["learning_progress_courses_" . $status] = [
                    "default" => true,
                    "txt"     => $title
                ];
            }
        } else {
            $columns["learning_progress_courses"] = [
                "default" => true,
                "txt"     => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("courses")
            ];
        }

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

		$data = self::ilias()->staff()->users()->getData(self::dic()->user()
			->getId(), $this->getFilterValues2(), $this->getOrderField(), $this->getOrderDirection(), $this->getOffset(), $this->getLimit());

        $data["data"] = array_map(function (array $row) : array {
            $row["pie"] = self::customInputGUIs()->learningProgressPie()->objIds()->withObjIds($row["learning_progress_courses"])->withUsrId($row["usr_id"]);

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
			"user" => [
				PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
				"setTitle" => $this->dic()->language()->txt("login") . "/" . $this->dic()->language()->txt("email") . "/" . $this->dic()->language()
						->txt("name")
			],
			"org_unit" => [
				PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
				PropertyFormGUI::PROPERTY_OPTIONS => [ 0 => "--"] + self::ilias()->staff()->users()
						->getOrgUnits(),
				PropertyFormGUI::PROPERTY_NOT_ADD => (!ilUserSearchOptions::_isEnabled("org_units")),
				"setTitle" => $this->dic()->language()->txt("obj_orgu"),
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("srlprep_staff_users");
        $this->setPrefix("srlprep_staff_users");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::dic()->language()->txt("users"));
	}


	/**
	 * @inheritdoc
	 */
	protected function fillRow(/*array*/ $row)/*: void*/ {
		$this->tpl->setCurrentBlock("column");
		$this->tpl->setVariable("COLUMN", self::output()->getHTML(self::dic()->ui()->factory()->image()
			->standard($row["usr_obj"]->getPersonalPicturePath("small"), $row["usr_obj"]->getPublicName())));
		$this->tpl->parseCurrentBlock();

		parent::fillRow($row);
	}


	/**
	 * @inheritdoc
	 */
	protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/ {
		self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, $row["usr_id"]);

		$actions->setId($row["usr_id"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function getRightHTML(): string {
		return "";
	}
}

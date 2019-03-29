<?php

namespace srag\Plugins\SrLpReport\Report;

use ilAdvancedSelectionListGUI;
use ilCSVWriter;
use ilExcel;
use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilMail;
use ilObject;
use ilObjectLP;
use ilObjOrgUnitTree;
use ilObjUserTracking;
use ilOrgUnitPathStorage;
use ilSelectInputGUI;
use ilTextInputGUI;
use ilTrQuery;
use ilUserDefinedFields;
use ilUserProfile;
use ilUtil;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;

/**
 * Class AbstractReport2TableGUI
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractReport2TableGUI extends AbstractReportTableGUI {

	/**
	 * AbstractReport2TableGUI constructor
	 *
	 * @param object $parent
	 * @param string $parent_cmd
	 */
	public function __construct($parent, string $parent_cmd) {

		$this->course = true;
		$this->ref_id = self::reports()->getReportObjRefId();
		$this->obj_id = self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId());
		$this->user_fields = [];

		$this->setShowRowsSelector(false);
		$this->setSelectAllCheckbox('usr_id');

		parent::__construct($parent, $parent_cmd);
	}


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false): string {
		switch ($column) {
			case "login":
				$column = $row[$column];
				if (!$raw_export) {
					$column = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($column, self::ilias()->staff()->user()
						->getLearningProgressLink(self::reports()->getReportObjRefId(), $row["usr_id"])));
				}

				return $column;

			case "org_units":
				$column = $row[$column];
				if (!$raw_export) {
					if (is_array($column)) {
						$column = implode(ilOrgUnitPathStorage::ORG_SEPARATOR, array_map(function (string $org_unit_title, int $org_unit_id): string {
							return self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($org_unit_title, self::ilias()->staff()
								->users()->getOrgUnitFilterLink($org_unit_id)));
						}, $column, array_keys($column)));
					} else {
						$column = strval($column);
					}
				} else {
					$column = implode(ilOrgUnitPathStorage::ORG_SEPARATOR, $column);
				}

				return $column;

			case "status":
				if ($raw_export) {
					return strval($this->getLearningProgressRepresentationExport(intval($row[$column])));
				} else {
					return strval($this->getLearningProgressRepresentation(intval($row[$column])));
				}

			default:
				return strval(is_array($row[$column]) ? implode(", ", $row[$column]) : $row[$column]);
		}
	}


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


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		$this->addColumn("", "");

		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				if (isset($column["icon"])) {
					$alt = self::dic()->language()->txt($column["type"]);
					$icon = self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($column["icon"], $alt));
					$column['txt'] = $icon . ' ' . $column['txt'];
				}

				$this->addColumn($column["txt"], ($column["sort"] ? $column["id"] : null), "", false, "", $column["path"]);
			}
		}

		$this->addColumn(self::dic()->language()->txt("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function getSelectableColumns2(): array {
		$cols = [];

		// default fields
		$cols["login"] = [
			"id" => "login",
			"sort" => "login",
			"txt" => self::dic()->language()->txt("login"),
			"default" => true,
			"all_reports" => true
		];

		$cols["firstname"] = [
			"id" => "firstname",
			"sort" => "firstname",
			"txt" => self::dic()->language()->txt("firstname"),
			"default" => true,
			"all_reports" => true
		];

		$cols["lastname"] = [
			"id" => "lastname",
			"sort" => "lastname",
			"txt" => self::dic()->language()->txt("lastname"),
			"default" => true,
			"all_reports" => true
		];

		$user_profile = new ilUserProfile();
		$user_profile->skipGroup("preferences");
		$user_profile->skipGroup("settings");
		$user_profile->skipGroup("interests");
		$user_standard_fields = $user_profile->getStandardFields();

		foreach ($user_standard_fields as $key => $field) {
			if (self::dic()->settings()->get("usr_settings_course_export_" . $key)) {
				$cols[$key] = [
					"id" => $key,
					"sort" => $key,
					"txt" => self::dic()->language()->txt($key),
					"default" => true,
					"all_reports" => true
				];
			}
		}

		// additional defined user data fields
		$user_defined_fields = ilUserDefinedFields::_getInstance();
		//if($a_in_course)
		//{
		$user_defined_fields_for_course = $user_defined_fields->getCourseExportableFields();
		//}
		/*else
		{
			$user_defined_fields = $user_defined_fields->getGroupExportableFields();
		}*/
		foreach ($user_defined_fields_for_course as $definition) {
			if ($definition["field_type"] != UDF_TYPE_WYSIWYG) {
				$cols["udf_" . $definition["field_id"]] = [
					"id" => "udf_" . $definition["field_id"],
					"sort" => "udf_" . $definition["field_id"],
					"txt" => $definition["field_name"],
					"default" => true,
					"all_reports" => true
				];

				$this->user_fields[] = $cols["udf_" . $definition["field_id"]];
			}
		}

		// show only if extended data was activated in lp settings
		$tracking = new ilObjUserTracking();

		/*
		if($tracking->hasExtendedData(ilObjUserTracking::EXTENDED_DATA_LAST_ACCESS))
		{
			$cols["first_access"] = [
				"id" => "first_access",
				"txt" => self::dic()->language()->txt("trac_first_access"),
				"default" => true];
			$cols["last_access"] = [
				"id" => "last_access",
				"txt" => self::dic()->language()->txt("trac_last_access"),
				"default" => true];
		}
		if($tracking->hasExtendedData(ilObjUserTracking::EXTENDED_DATA_READ_COUNT))
		{
			$cols["read_count"] = [
				"id" => "read_count",
				"txt" => self::dic()->language()->txt("trac_read_count"),
				"default" => true];
		}
		if($tracking->hasExtendedData(ilObjUserTracking::EXTENDED_DATA_SPENT_SECONDS) &&
			ilObjectLP::supportsSpentSeconds($this->type))
		{
			$cols["spent_seconds"] = [
				"id" => "spent_seconds",
				"txt" => self::dic()->language()->txt("trac_spent_seconds"),
				"default" => true];
		}*/

		/*if($this->isPercentageAvailable($this->obj_id))
		{
			$cols["percentage"] = [
				"txt" => self::dic()->language()->txt("trac_percentage"),
				"default" => true];
		}*/

		// do not show status if learning progress is deactivated

		$olp = ilObjectLP::getInstance($this->obj_id);

		if ($olp->isActive()) {

			$type = self::dic()->objDataCache()->lookupType($this->obj_id);
			$icon = ilObject::_getIcon("", "tiny", $type);

			$cols["status"] = [
				"id" => "status",
				"sort" => "status",
				"txt" => self::dic()->language()->txt("learning_progress") . " " . self::dic()->objDataCache()->lookupTitle($this->obj_id),
				"default" => true,
				"all_reports" => true,
				"icon" => $icon
			];
		}

		return $cols;
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->setLimit(99999999999, 99999999999);
		$this->determineOffsetAndOrder(true);

		$filter = $this->getFilterValues2();

		$additional_fields = $this->getSelectedColumns();

		$check_agreement = false;

		$tr_data = ilTrQuery::getUserDataForObject($this->ref_id, ilUtil::stripSlashes($this->getOrderField()), ilUtil::stripSlashes($this->getOrderDirection()), ilUtil::stripSlashes($this->getOffset()), ilUtil::stripSlashes($this->getLimit()), $filter, $additional_fields, $check_agreement, $this->user_fields);

		if (count($tr_data["set"]) == 0 && $this->getOffset() > 0) {
			$this->resetOffset();
			$tr_data = ilTrQuery::getUserDataForObject($this->ref_id, ilUtil::stripSlashes($this->getOrderField()), ilUtil::stripSlashes($this->getOrderDirection()), ilUtil::stripSlashes($this->getOffset()), ilUtil::stripSlashes($this->getLimit()), $filter, $additional_fields, $check_agreement, $this->user_fields);
		}

		foreach ($this->user_fields as $key => $value) {
			if ($filter[$value['id']]) {

				foreach ($tr_data["set"] as $key => $data) {
					if ($data[$value['id']] != $filter[$value['id']]) {
						unset($tr_data["set"][$key]);
						$tr_data["cnt"] = $tr_data["cnt"] - 1;
					}
				}
			}
		}

		foreach ($tr_data["set"] as &$row) {
			$row["org_units"] = array_map(function (int $org_unit_id): string {
				return self::dic()->objDataCache()->lookupTitle($org_unit_id);
			}, ilObjOrgUnitTree::_getInstance()->getOrgUnitOfUser($row["usr_id"]));
		}

		$this->setMaxCount($tr_data["cnt"]);
		$this->setData($tr_data["set"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {
		foreach ($this->getSelectableColumns2() as $key => $value) {

			if (!$value['all_reports']) {
				continue;
			}

			switch ($key) {
				case "status":
					$this->filter_fields[$key] = [
						PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
						PropertyFormGUI::PROPERTY_OPTIONS => [
							0 => self::dic()->language()->txt("trac_all"),
							ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED),
							ilLPStatus::LP_STATUS_IN_PROGRESS_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_IN_PROGRESS),
							ilLPStatus::LP_STATUS_COMPLETED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_COMPLETED)
							//ilLPStatus::LP_STATUS_FAILED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_FAILED)
						],
						"setTitle" => $value['txt']
					];
					break;
				default:
					$this->filter_fields[$key] = [
						PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
						"setTitle" => $value['txt']
					];
					break;
			}
		}
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		$this->tpl->setCurrentBlock("column");

		parent::fillRow($row);

		$this->tpl->setCurrentBlock("checkbox");
		$this->tpl->setVariable("CHECKBOX_POST_VAR", 'usr_id');
		$this->tpl->setVariable("ID", $row['usr_id']);
		$this->tpl->parseCurrentBlock();

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::dic()->language()->txt("actions"));
		$actions->setAsynch(true);
		$this->extendsActionsMenu($actions, $row);
		$actions->setAsynchUrl(str_replace("\\", "\\\\", self::dic()->ctrl()
			->getLinkTarget($this->parent_obj, AbstractStaffGUI::CMD_GET_ACTIONS, "", true)));
		$this->tpl->setVariable("COLUMN", self::output()->getHTML($actions));
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		// see ilObjCourseGUI::addMailToMemberButton()
		$mail = new ilMail(self::dic()->user()->getId());
		if (self::dic()->rbacsystem()->checkAccess("internal_mail", $mail->getMailObjectReferenceId())) {
			$this->addMultiCommand(AbstractReportGUI::CMD_MAIL_SELECTED_USERS, $this->lng->txt("send_mail"));
		}
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 * @param array   $result
	 */
	protected function fillRowExcel(ilExcel $excel, /*int*/
		&$row, /*array*/
		$result)/*: void*/ {
		$col = 0;
		foreach ($this->getSelectableColumns() as $column) {
			$excel->setCell($row, $col, $this->getColumnValue($column["id"], $result, true));
			$col ++;
		}
	}


	/**
	 * @param ilCSVWriter $csv
	 * @param array       $row
	 */
	protected function fillRowCSV(/*ilCSVWriter*/
		$csv, /*array*/
		$row)/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			$csv->addColumn($this->getColumnValue($column["id"], $row, true));
		}

		$csv->addRow();
	}


	/**
	 * @inheritdoc
	 */
	protected function getRightHTML(): string {
		return ReportGUI::getLegendHTML();
	}
}

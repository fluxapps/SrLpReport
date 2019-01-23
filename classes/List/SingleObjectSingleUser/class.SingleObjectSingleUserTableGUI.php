<?php

use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;

/**
 * Class MatrixSingleObjectSingleUserTableGUI
 *
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 */
class MatrixSingleObjectSingleUserTableGUI extends TableGUI {

	public function __construct($parent, /*string*/
		$parent_cmd) {

		$this->setExternalSorting(false);
		$this->setExternalSegmentation(false);
		$this->setLimit(99999999999, 99999999999);
		$this->determineOffsetAndOrder(false);

		$this->course = true;
		$this->ref_id = $_GET['ref_id'];
		$this->obj_id = ilObject::_lookupObjectId($_GET['ref_id']);
		$this->user_fields = array();

		$this->setShowRowsSelector(false);
		$this->disable(false);

		parent::__construct($parent, /*string*/
			$parent_cmd);
	}


	protected function initId() {
		$this->setId('srrep_msu');
		$this->setPrefix('srrep_msu');
	}


	protected function initFilterFields() {


		$item = new ilTextInputGUI(self::dic()->language()->txt("title"), "object");
		$this->addFilterItem($item);
		$item->readFromSession();
		$this->filter["object"] = $item->getValue();

		self::dic()->language()->loadLanguageModule('trac');
		$item = new ilSelectInputGUI(self::dic()->language()->txt("status"), "status");
		$item->setOptions(array(
			"all" => self::dic()->language()->txt("trac_all"),
			ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED),
			ilLPStatus::LP_STATUS_IN_PROGRESS_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_IN_PROGRESS),
			ilLPStatus::LP_STATUS_COMPLETED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_COMPLETED),
			ilLPStatus::LP_STATUS_FAILED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_FAILED)
		));
		$this->addFilterItem($item);
		$item->readFromSession();

		if ($item->getValue()) {
			$this->filter["status"] = $item->getValue();
			$this->filter["status"] --;
		}
	}


	protected function initColumns()/*: void*/ {

		foreach ($this->getStandardColumns() as $column) {
			$this->addColumn($column["txt"], ($column["sort"] ? $column["id"] : NULL), "", false, "", $column["path"]);
		}
	}



	protected function getStandardColumns() {

		// default fields
		$cols["object"] = array(
			"id" => "object",
			"sort" => "object",
			"txt" => self::dic()->language()->txt("title"),
			"default" => true,
		);

		// default fields
		$cols["status"] = array(
			"id" => "status",
			"sort" => "status",
			"txt" => self::dic()->language()->txt("status"),
			"default" => true,
		);

		return $cols;
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		$this->tpl->setCurrentBlock("column");

		foreach ($this->getStandardColumns() as $column) {
			$column = $this->getColumnValue($column["id"], $row);

			if (!empty($column)) {
				$this->tpl->setVariable("COLUMN", $column);
			} else {
				$this->tpl->setVariable("COLUMN", " ");
			}

			$this->tpl->parseCurrentBlock();
		}
	}


	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false) {



		if ($column == "object") {
			if($raw_export) {
				return $row['obj_title'];
			}
			return ilUtil::img($row['obj_icon'], $row['obj_title']) . " " . $row['obj_title'];
		}

		if ($column == "status") {
			if($raw_export) {
				return $row['status_text'];
			}
			return ilUtil::img($row['status_icon'], $row['status_text']) . " " . $row['status_text'];
		}

		return parent::getColumnValue($column, /*array*/
			$row, /*bool*/
			$raw_export);
	}


	protected function initData() {

		self::dic()->language()->loadLanguageModule('trac');

		$collection = ilTrQuery::getObjectIds($this->obj_id, $this->ref_id, true, true, [ $_GET['usr_id'] ]);
		$row = [];

		if (count($collection["object_ids"]) > 0) {
			foreach ($collection["object_ids"] as $collection_obj_id) {

				if ($collection_obj_id == $this->obj_id) {
					continue;
				}

				if ($this->filter["status"] !== "all" && (!empty($this->filter["status"]) || $this->filter["status"] === 0)) {
					if ($this->filter["status"] !== ilLPStatusWrapper::_determineStatus($collection_obj_id, $_GET['usr_id'])) {
						continue;
					}
				}

				if (strlen($this->filter["object"]) > 0) {
					if (!preg_match('[' . strtolower($this->filter["object"]) . ']', strtolower(self::dic()->objDataCache()
						->lookupTitle($collection_obj_id)))) {
						continue;
					}
				}

				$row[$collection_obj_id]['status'] = ilLPStatusWrapper::_determineStatus($collection_obj_id, $_GET['usr_id']);
				$row[$collection_obj_id]['status_text'] = ilLearningProgressBaseGUI::_getStatusText(ilLPStatusWrapper::_determineStatus($collection_obj_id, $_GET['usr_id']));
				$row[$collection_obj_id]['status_icon'] = ilLearningProgressBaseGUI::_getImagePathForStatus(ilLPStatusWrapper::_determineStatus($collection_obj_id, $_GET['usr_id']));;
				$row[$collection_obj_id]['obj_title'] = self::dic()->objDataCache()->lookupTitle($collection_obj_id);
				$row[$collection_obj_id]['obj_icon'] = ilObject::_getIcon("", "tiny", self::dic()->objDataCache()->lookupType($collection_obj_id));
			}
		}

		$this->setMaxCount(count($row));
		$this->setData($row);

		return false;
	}


	protected function getSelectableColumns2() {
		return $this->getStandardColumns();
	}




	protected function initTitle() {

	}

	public function getTableFooterAndHeaderHtml() {

		self::dic()->language()->loadLanguageModule('trac');

		$tpl = self::plugin()->template("Report/report.html",false,false);
		$tpl->setVariable("REPORT",$this->table->getHTML());
		$tpl->setVariable('LEGEND',ilSrLpReportGUI::getLegendHTML());

		return self::output()->getHTML($tpl);
	}

	/**
	 *
	 */
	protected function initExport()/*: void*/ {
		$this->setExportFormats([self::EXPORT_EXCEL,self::EXPORT_CSV]);
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
	 * @param int $status
	 * @param int $percentage
	 *
	 * @return string
	 */
	protected function getLearningProgressRepresentationExport($status = 0, $percentage = 0): string {

		if ($percentage > 0) {
			return $percentage . "%";
		}


		switch ($status) {
			case 0:
				return self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED);
			default:
				return ilLearningProgressBaseGUI::_getStatusText($status);
		}

		return "";
	}


}

?>
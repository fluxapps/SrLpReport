<?php

use srag\Plugins\SrLpReport\ReportTableGUI\AbstractReportTableGUI;

/**
 * Class MatrixTableGUI
 *
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 */

class MatrixTableGUI extends AbstractReportTableGUI
{



	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false) {


		if(count(explode('obj_', $column)) == 2) {
			$percentage = $row[$column."_perc"];
			return $this->getLearningProgressRepresentation($row[$column],$percentage);
		}

		return parent::getColumnValue($column, /*array*/
			$row, /*bool*/
			$raw_export = false);
	}


	protected function getSelectableColumns2() {
		$cols = parent::getSelectableColumns2();


		include_once("./Services/Tracking/classes/class.ilTrQuery.php");
		$collection = ilTrQuery::getObjectIds($this->obj_id, $this->ref_id, true);

		if(count($collection['object_ids'] > 0))
		{
			$tmp_cols = array();
			foreach($collection['object_ids'] as $obj_id)
			{
				if($obj_id == $this->obj_id)
				{
					$parent = array("txt" => $this->lng->txt("status"),
						"default" => true);
				}
				else
				{
					$no_perm = false;

					$ref_id = $collection['ref_ids'][$obj_id];
					include_once './Services/Tracking/classes/class.ilLearningProgressAccess.php';
					if($ref_id &&
						!ilLearningProgressAccess::checkPermission('read_learning_progress', $ref_id))
					{
						$no_perm = true;
						$this->privacy_cols[] = $obj_id;
					}

					$title = self::dic()->objDataCache()->lookupTitle($obj_id);
					$type = self::dic()->objDataCache()->lookupType($obj_id);
					$icon = ilObject::_getIcon("", "tiny", $type);
					if($type == "sess")
					{
						include_once "Modules/Session/classes/class.ilObjSession.php";
						$sess = new ilObjSession($obj_id, false);
						$title = $sess->getPresentationTitle();
					}

					// #16453
					$relpath = null;
					include_once './Services/Tree/classes/class.ilPathGUI.php';
					$path = new ilPathGUI();
					$path = $path->getPath($this->ref_id, $ref_id);
					if($path)
					{
						$relpath = $this->lng->txt('path').': '.$path;
					}


					$cols["obj_".$obj_id] = array(
						"id" => "obj_".$obj_id,
						"sort" => "obj_".$obj_id,
						"txt" => $title,
						"default" => true,
						"no_permission" => $no_perm,
						"path" => $relpath,
						"icon" => $icon);
				}
			}

			if($parent)
			{
				$columns["obj_".$this->obj_id] = $parent;
			}
		}


		return $cols;
	}

	protected function initData()
	{
		$this->setExternalSorting(false);
		$this->setExternalSegmentation(false);
		$this->setLimit(99999999999,99999999999);
		$this->determineOffsetAndOrder(false);


		$collection = ilTrQuery::getObjectIds($this->obj_id, $this->ref_id, true);
		if($collection["object_ids"])
		{
			// we need these for the timing warnings
			$this->ref_ids = $collection["ref_ids"];
			$additional_fields = $this->getStandardReportColumns();
			$additional_fields[] = 'status';

			$data = ilTrQuery::getUserObjectMatrix($this->ref_id, $collection["object_ids"],  array(), $additional_fields, $this->user_fields, false);



			// percentage export
			if($data["set"])
			{
				$this->perc_map = array();
				foreach($data["set"] as $row_idx => $row)
				{
					foreach($row as $column => $value)
					{
						if(substr($column, -5) == "_perc")
						{
							$obj_id = explode("_", $column);
							$obj_id = (int)$obj_id[1];

							// #18673
							if(!$this->isPercentageAvailable($obj_id) ||
								!(int)$value)
							{
								unset($data["set"][$row_idx][$column]);
							}
							else
							{
								$this->perc_map[$obj_id] = true;
							}
						}
					}
				}
			}


			//filter
			$table_data = array();
			if(count($data["set"]) > 0) {
				foreach($data["set"] as $row) {

					$filtered = false;
					foreach($this->filter as $filter_field => $filter) {
						if ((!empty($filter) || is_numeric($filter)) && $row[$filter_field] != $filter) {
							$filtered = true;
						}
					}

					if(!$filtered) {
						$table_data[] = $row;
					}

				}
			}



			$this->setMaxCount(count($table_data));
			$this->setData($table_data);

		}
		return false;
	}


	/**
	 * Get selected columns
	 *
	 * @param
	 * @return
	 */
	function getStandardReportColumns()
	{
		$scol = array();
		foreach ($this->selected_column as $k => $v)
		{
			if (key_exists($k,parent::getSelectableColumns2()))
			{
				$scol[$k] = $k;
			}
		}
		return $scol;
	}

	protected function isPercentageAvailable($a_obj_id)
	{
		// :TODO:
		$olp = ilObjectLP::getInstance($a_obj_id);
		$mode = $olp->getCurrentMode();
		if(in_array($mode, array(ilLPObjSettings::LP_MODE_TLT,
			ilLPObjSettings::LP_MODE_VISITS,
			ilLPObjSettings::LP_MODE_SCORM,
			ilLPObjSettings::LP_MODE_VISITED_PAGES,
			ilLPObjSettings::LP_MODE_TEST_PASSED)))
		{
			return true;
		}
		return false;
	}

	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {




		$this->tpl->setCurrentBlock("column");

		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$column = $this->getColumnValue($column["id"], $row);

				if (!empty($column)) {
					$this->tpl->setVariable("COLUMN", $column);
				} else {
					$this->tpl->setVariable("COLUMN", " ");
				}

				$this->tpl->parseCurrentBlock();
			}
		}

		$this->tpl->setCurrentBlock("checkbox");
		$this->tpl->setVariable("CHECKBOX_POST_VAR", 'usr_id');
		$this->tpl->setVariable("ID", $row['usr_id']);
		$this->tpl->parseCurrentBlock();
	}


	protected function initId() {
		$this->setId('srcrslp_matrix');
		$this->setPrefix('srcrslp_matrix');
	}
}
?>
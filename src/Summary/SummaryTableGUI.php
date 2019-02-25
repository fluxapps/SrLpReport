<?php

namespace srag\Plugins\SrLpReport\Summary;

use ilLPObjSettings;
use ilObject;
use ilObjectLP;
use ilSrLpReportPlugin;
use ilTrQuery;
use ilUtil;
use srag\CustomInputGUIs\SrLpReport\CustomInputGUIsTrait;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;

/**
 * Class SummaryTableGUI
 *
 * @package srag\Plugins\SrLpReport\Summary
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SummaryTableGUI extends TableGUI {

	use CustomInputGUIsTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;


	/**
	 * SummaryTableGUI constructor
	 *
	 * @param object $parent
	 * @param string $parent_cmd
	 */
	public function __construct($parent, /*string*/
		$parent_cmd) {

		$this->ref_id = ReportFactory::getReportObjRefId();
		$this->obj_id = ilObject::_lookupObjectId(ReportFactory::getReportObjRefId());
		$this->user_fields = [];

		$this->setShowRowsSelector(false);

		parent::__construct($parent, /*string*/
			$parent_cmd);
	}


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false): string {
		switch ($column) {
			case "status":
				if (!$raw_export) {
					return self::output()->getHTML(self::customInputGUIs()->learningProgressPie()->count()->withCount($row["status"])
						->withId($row['obj_id'])->withShowLegend(true));
				} else {
					return "";
				}
			default:
				return strval(is_array($row[$column]) ? implode(", ", $row[$column]) : $row[$column]);
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function getSelectableColumns2(): array {
		$cols = [];

		// default fields
		$cols["title"] = array(
			"id" => "title",
			"sort" => "title",
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
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$olp = ilObjectLP::getInstance(ilObject::_lookupObjId($this->ref_id));
		if ($olp->getCurrentMode() == ilLPObjSettings::LP_MODE_COLLECTION_MANUAL
			|| $olp->getCurrentMode() == ilLPObjSettings::LP_MODE_COLLECTION
			|| $olp->getCurrentMode() == ilLPObjSettings::LP_MODE_MANUAL_BY_TUTOR) {
			$collection = $olp->getCollectionInstance();
			$preselected_obj_ids[$this->obj_id][] = $this->ref_id;
			foreach ($collection->getItems() as $item => $item_info) {
				$tmp_lp = ilObjectLP::getInstance(ilObject::_lookupObjId($item_info));
				if ($tmp_lp->isActive()) {
					$preselected_obj_ids[ilObject::_lookupObjId($item_info)][] = $item_info;
				}
			}
			//$filter = $this->getCurrentFilter();
		}

		$data = ilTrQuery::getObjectsSummaryForObject($this->obj_id, $this->ref_id, ilUtil::stripSlashes($this->getOrderField()), ilUtil::stripSlashes($this->getOrderDirection()), ilUtil::stripSlashes($this->getOffset()), ilUtil::stripSlashes($this->getLimit()), [], $this->getSelectedColumns(), $preselected_obj_ids);

		$this->setData($data["set"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId('srcrslp_summary');
		$this->setPrefix('srcrslp_summary');
	}
}

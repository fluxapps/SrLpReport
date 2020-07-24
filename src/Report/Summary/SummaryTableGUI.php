<?php

namespace srag\Plugins\SrLpReport\Report\Summary;

use ilAdvancedSelectionListGUI;
use ilExcel;
use ilLPObjSettings;
use ilObjectLP;
use ilTrQuery;
use ilUtil;
use srag\Plugins\SrLpReport\Report\AbstractReport2TableGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;

/**
 * Class SummaryTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report\Summary
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SummaryTableGUI extends AbstractReport2TableGUI
{

    /**
     * SummaryTableGUI constructor
     *
     * @param SummaryReportGUI $parent
     * @param string           $parent_cmd
     */
    public function __construct(SummaryReportGUI $parent, string $parent_cmd)
    {

        $this->ref_id = self::reports()->getReportObjRefId();
        $this->obj_id = self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId());
        $this->user_fields = [];

        $this->setShowRowsSelector(false);

        parent::__construct($parent, $parent_cmd);

        $this->setSelectAllCheckbox(null);
    }


    /**
     * @inheritdoc
     */
    protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch (true) {
            case $column === "title":
                $column = $row[$column];

                return $column;

            case $column === "status":
                if (!$format) {
                    return self::output()->getHTML($row["pie"]);
                } else {
                    return "";
                }

            case $column === "status_count":
                return strval($row["pie"]->getData()["count"]);

            case strpos($column, "status_") === 0:
                $status = intval(substr($column, strlen("status_")));

                return strval($row["pie"]->getData()["data"][$status]["value"]);

            default:
                return strval(is_array($row[$column]) ? implode(", ", $row[$column]) : $row[$column]);
        }
    }


    /**
     * @inheritdoc
     */
    protected function getSelectableColumns2() : array
    {
        $cols = [];

        // default fields
        $cols["title"] = [
            "id"      => "title",
            "sort"    => true,
            "txt"     => self::dic()->language()->txt("title"),
            "default" => true
        ];

        if ($this->getExportMode()) {
            $cols["status_count"] = [
                "default" => true,
                "txt"     => self::dic()->language()->txt("total")
            ];
            foreach (self::learningProgressPieUI()->count()->getTitles() as $status => $title) {
                $cols["status_" . $status] = [
                    "id"      => "status_" . $status,
                    "sort"    => true,
                    "txt"     => $title,
                    "default" => true
                ];
            }
        } else {
            $cols["status"] = [
                "id"      => "status",
                "sort"    => false,
                "txt"     => self::dic()->language()->txt("status"),
                "default" => true
            ];
        }

        return $cols;
    }


    /**
     * @inheritdoc
     */
    protected function processData(bool $limit = true) : array
    {
        $olp = ilObjectLP::getInstance(self::dic()->objDataCache()->lookupObjId($this->ref_id));
        if ($olp->getCurrentMode() == ilLPObjSettings::LP_MODE_COLLECTION_MANUAL
            || $olp->getCurrentMode() == ilLPObjSettings::LP_MODE_COLLECTION
            || $olp->getCurrentMode() == ilLPObjSettings::LP_MODE_MANUAL_BY_TUTOR
        ) {
            $collection = $olp->getCollectionInstance();
            $preselected_obj_ids[$this->obj_id][] = $this->ref_id;
            foreach ($collection->getItems() as $item => $item_info) {
                $tmp_lp = ilObjectLP::getInstance(self::dic()->objDataCache()->lookupObjId($item_info));
                if ($tmp_lp->isActive()) {
                    $preselected_obj_ids[self::dic()->objDataCache()->lookupObjId($item_info)][] = $item_info;
                }
            }
            //$filter = $this->getCurrentFilter();
        }

        $data = ilTrQuery::getObjectsSummaryForObject($this->obj_id, $this->ref_id, ilUtil::stripSlashes($this->getOrderField()), ilUtil::stripSlashes($this->getOrderDirection()),
            ($limit ? ilUtil::stripSlashes($this->getOffset()) : null), ($limit ? ilUtil::stripSlashes($this->getLimit()) : null), [], $this->getSelectedColumns(), $preselected_obj_ids);

        $data["set"] = array_map(function (array $row) : array {
            $row["pie"] = self::learningProgressPieUI()->count()->withCount($row["status"]);

            if ($this->getExportMode()) {
                $row["pie"] = $row["pie"]->withShowEmpty(true);
            }

            return $row;
        }, $data["set"]);

        return [
            "cnt" => count($data["set"]),
            "set" => $data["set"]
        ];
    }


    /**
     * @inheritdoc
     */
    protected function initFilterFields()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function initTitle()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {
        $this->setId('srlprep_summary');
        $this->setPrefix('srlprep_summary');
    }


    /**
     * @inheritdoc
     */
    protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function getRightHTML() : string
    {
        return ReportGUI::getLegendHTML();
    }


    /**
     * @inheritdoc
     */
    protected function initColumns()/*: void*/
    {
        AbstractReportTableGUI::initColumns();
    }


    /**
     * @inheritdoc
     */
    protected function initCommands()/*: void*/
    {
        AbstractReportTableGUI::initCommands();
    }


    /**
     * @inheritdoc
     */
    protected function fillRow(/*array*/ $row)/*: void*/
    {
        AbstractReportTableGUI::fillRow($row);
    }


    /**
     * @inheritdoc
     */
    protected function fillRowExcel(ilExcel $excel, /*int*/ &$row, /*array*/ $result)/*: void*/
    {
        AbstractReportTableGUI::fillRowExcel( $excel, $row, $result);
    }


    /**
     * @inheritdoc
     */
    protected function fillRowCSV(/*ilCSVWriter*/ $csv, /*array*/ $row)/*: void*/
    {
        AbstractReportTableGUI::fillRowCSV($csv, $row);
    }
}

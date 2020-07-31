<?php

namespace srag\Plugins\SrLpReport\Report\Matrix\Single;

use ilAdvancedSelectionListGUI;
use ilCSVWriter;
use ilExcel;
use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilLPStatusWrapper;
use ilObject;
use ilPublicUserProfileGUI;
use ilSelectInputGUI;
use ilTextInputGUI;
use ilTrQuery;
use srag\CommentsUI\SrLpReport\Utils\CommentsUITrait;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Comment\Ctrl\ReportCtrl;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;

/**
 * Class MatrixSingleTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report\Matrix\Single
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MatrixSingleTableGUI extends AbstractReportTableGUI
{

    use CommentsUITrait;


    /**
     * MatrixSingleTableGUI constructor
     *
     * @param MatrixSingleReportGUI $parent
     * @param string                $parent_cmd
     */
    public function __construct(MatrixSingleReportGUI $parent, string $parent_cmd)
    {
        $this->setExternalSorting(false);
        $this->setExternalSegmentation(false);
        $this->setLimit(99999999999, 99999999999);
        $this->determineOffsetAndOrder(false);

        $this->course = true;
        $this->ref_id = self::reports()->getReportObjRefId();
        $this->obj_id = self::dic()->objDataCache()->lookupObjId($this->ref_id);
        $this->user_fields = [];

        $this->setShowRowsSelector(false);
        $this->disable(false);

        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {
        $this->setId('srrep_msu');
        $this->setPrefix('srrep_msu');
    }


    /**
     * @inheritdoc
     */
    protected function initFilterFields()/*: void*/
    {
        $this->filter_fields = [
            "object" => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
                "setTitle"                      => self::dic()->language()->txt("title")
            ],
            "status" => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                    0                                           => self::dic()->language()->txt("trac_all"),
                    ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED),
                    ilLPStatus::LP_STATUS_IN_PROGRESS_NUM + 1   => self::dic()->language()->txt(ilLPStatus::LP_STATUS_IN_PROGRESS),
                    ilLPStatus::LP_STATUS_COMPLETED_NUM + 1     => self::dic()->language()->txt(ilLPStatus::LP_STATUS_COMPLETED)
                    //ilLPStatus::LP_STATUS_FAILED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_FAILED)
                ],
                "setTitle"                        => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("objects")
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    protected function initColumns()/*: void*/
    {
        foreach ($this->getStandardColumns() as $column) {
            $this->addColumn($column["txt"], ($column["sort"] ? $column["id"] : null), "", false, "", $column["path"]);
        }
    }


    /**
     * @return array
     */
    protected function getStandardColumns() : array
    {
        // default fields
        $cols["object"] = [
            "id"      => "object",
            "sort"    => "object",
            "txt"     => self::dic()->language()->txt("title"),
            "default" => true,
        ];

        // default fields
        $cols["status"] = [
            "id"      => "status",
            "sort"    => "status",
            "txt"     => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("objects"),
            "default" => true,
        ];

        $cols["actions"] = [
            "id"      => "actions",
            "sort"    => "actions",
            "txt"     => self::dic()->language()->txt("actions"),
            "default" => true,
        ];

        return $cols;
    }


    /**
     * @param array $row
     */
    protected function fillRow(/*array*/ $row)/*: void*/
    {
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


    /**
     * @inheritdoc
     */
    protected function getColumnValue(string $column, /*array*/ $row, int $format = self::DEFAULT_FORMAT) : string
    {
        if ($column === "object") {
            if ($format) {
                return $row['obj_title'];
            }

            return self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($row['obj_icon'], $row['obj_title'])) . " "
                . $row['obj_title'];
        }

        if ($column == "status") {

            if ($format) {
                return strval($this->getLearningProgressRepresentationExport(intval($row['status']), 0));
            } else {
                return strval($this->getLearningProgressRepresentation(intval($row['status']), 0));
            }

            if ($format) {
                return $row['status_text'];
            }

            return self::output()->getHTML(self::dic()->ui()->factory()->image()->standard($row['status_icon'], $row['status_text'])) . " "
                . $row['status_text'];
        }

        if ($column === "actions") {
            return self::output()->getHTML(self::reports()->getCellActions($row["ref_id"], self::reports()->getUsrId()));
        }

        return "";
    }


    /**
     * @inheritdoc
     */
    protected function initData()/*: void*/
    {
        $row = [];

        $filter = $this->getFilterValues2();

        $ref_ids = self::reports()->getChilds($this->ref_id, [self::reports()->getUsrId()]);

        if (!empty($ref_ids)) {
            foreach ($ref_ids as $ref_id) {
                $collection_obj_id = self::dic()->objDataCache()->lookupObjId($ref_id);
                if ($collection_obj_id == $this->obj_id) {
                    continue;
                }

                if (isset($filter["status"])) {
                    if ($filter["status"] !== ilLPStatusWrapper::_determineStatus($collection_obj_id, self::reports()->getUsrId())) {
                        continue;
                    }
                }

                if (strlen($filter["object"]) > 0) {
                    if (!preg_match('[' . strtolower($filter["object"]) . ']', strtolower(self::dic()->objDataCache()
                        ->lookupTitle($collection_obj_id)))
                    ) {
                        continue;
                    }
                }

                $row[$collection_obj_id] = [];

                $row[$collection_obj_id]["obj_id"] = $collection_obj_id;

                $row[$collection_obj_id]["ref_id"] = $ref_id;

                $row[$collection_obj_id]['status'] = ilLPStatusWrapper::_determineStatus($collection_obj_id, self::reports()->getUsrId());
                $row[$collection_obj_id]['status_text'] = ilLearningProgressBaseGUI::_getStatusText(ilLPStatusWrapper::_determineStatus($collection_obj_id, self::reports()
                    ->getUsrId()));
                $row[$collection_obj_id]['status_icon'] = ilLearningProgressBaseGUI::_getImagePathForStatus(ilLPStatusWrapper::_determineStatus($collection_obj_id, self::reports()
                    ->getUsrId()));
                $row[$collection_obj_id]['obj_title'] = self::dic()->objDataCache()->lookupTitle($collection_obj_id);
                $row[$collection_obj_id]['obj_icon'] = ilObject::_getIcon("", "tiny", self::dic()->objDataCache()->lookupType($collection_obj_id));
            }
        }

        $this->setMaxCount(count($row));
        $this->setData($row);
    }


    /**
     * @inheritdoc
     */
    protected function getSelectableColumns2() : array
    {
        return $this->getStandardColumns();
    }


    /**
     * @inheritdoc
     */
    protected function initTitle()
    {
        $this->setTitle(self::dic()->objDataCache()->lookupTitle(self::reports()->getUsrId()));
    }


    /**
     * @param ilExcel $excel
     * @param int     $row
     * @param array   $result
     */
    protected function fillRowExcel(ilExcel $excel, /*int*/ &$row, /*array*/ $result)/*: void*/
    {
        $col = 0;
        foreach ($this->getSelectableColumns() as $column) {
            $excel->setCell($row, $col, $this->getColumnValue($column["id"], $result, true));
            $col++;
        }
    }


    /**
     * @param ilCSVWriter $csv
     * @param array       $row
     */
    protected function fillRowCSV(/*ilCSVWriter*/ $csv, /*array*/ $row)/*: void*/
    {
        foreach ($this->getSelectableColumns() as $column) {
            $csv->addColumn($this->getColumnValue($column["id"], $row, true));
        }

        $csv->addRow();
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
    public function getRightHTML() : string
    {
        return self::output()->getHTML([
            self::learningProgressPieUI()->objIds()->withObjIds(array_keys($this->row_data))->withUsrId(self::reports()->getUsrId()),
            "<br>",
            (new ilPublicUserProfileGUI(self::reports()->getUsrId()))->getEmbeddable(),
            "<br>",
            ReportGUI::getLegendHTML(),
            "<br>",
            Config::getField(Config::KEY_ENABLE_COMMENTS) ? self::commentsUI()->withCtrlClass(new ReportCtrl()) : ""
        ]);
    }
}

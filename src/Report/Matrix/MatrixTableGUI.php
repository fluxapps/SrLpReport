<?php

namespace srag\Plugins\SrLpReport\Report\Matrix;

use ilAdvancedSelectionListGUI;
use ilExcel;
use ilLearningProgressAccess;
use ilLPObjSettings;
use ilObject;
use ilObjectLP;
use ilObjOrgUnitTree;
use ilObjSession;
use ilPathGUI;
use ilTrQuery;
use srag\Plugins\SrLpReport\Report\AbstractReport2TableGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;

/**
 * Class MatrixTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report\Matrix
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 */
class MatrixTableGUI extends AbstractReport2TableGUI
{

    /**
     * @inheritdoc
     */
    protected function getColumnValue(/*string*/
        $column, /*array*/
        $row, /*int*/
        $format = self::DEFAULT_FORMAT
    ) : string {
        if ($column == 'status') {
            if ($format) {
                return strval($this->getLearningProgressRepresentationExport(intval($row['obj_' . self::dic()->objDataCache()
                    ->lookupObjId($this->ref_id)]), 0));
            } else {
                return strval($this->getLearningProgressRepresentation(intval($row['obj_' . self::dic()->objDataCache()
                    ->lookupObjId($this->ref_id)]), 0));
            }
        }

        if (count(explode('obj_', $column)) == 2) {
            /*echo $column;
            echo $row[$column];
            echo strval($this->getLearningProgressRepresentationExport(intval($row[$column]), 100));
            exit;*/
            $percentage = intval($row[$column . "_perc"]);
            if ($format) {
                return strval($this->getLearningProgressRepresentationExport(intval($row[$column]), $percentage));
            } else {
                return $this->getLearningProgressRepresentation(intval($row[$column]), $percentage);
            }
        }

        return parent::getColumnValue($column, $row, $format);
    }


    /**
     * @inheritdoc
     */
    protected function getSelectableColumns2() : array
    {
        $cols = parent::getSelectableColumns2();

        $collection = ilTrQuery::getObjectIds($this->obj_id, $this->ref_id, true);

        if (count($collection['object_ids'] > 0)) {
            $tmp_cols = [];
            foreach ($collection['object_ids'] as $obj_id) {
                if ($obj_id == $this->obj_id) {
                    $parent = [
                        "txt"     => $this->lng->txt("status"),
                        "default" => true
                    ];
                } else {
                    $no_perm = false;

                    $ref_id = $collection['ref_ids'][$obj_id];
                    if ($ref_id
                        && !ilLearningProgressAccess::checkPermission('read_learning_progress', $ref_id)
                    ) {
                        $no_perm = true;
                        $this->privacy_cols[] = $obj_id;
                    }

                    $title = self::dic()->objDataCache()->lookupTitle($obj_id);
                    $type = self::dic()->objDataCache()->lookupType($obj_id);
                    $icon = ilObject::_getIcon("", "tiny", $type);
                    if ($type == "sess") {
                        $sess = new ilObjSession($obj_id, false);
                        $title = $sess->getPresentationTitle();
                    }

                    // #16453
                    $relpath = null;
                    $path = new ilPathGUI();
                    $path = $path->getPath($this->ref_id, $ref_id);
                    if ($path) {
                        $relpath = $this->lng->txt('path') . ': ' . $path;
                    }

                    $cols["obj_" . $obj_id] = [
                        "id"            => "obj_" . $obj_id,
                        "sort"          => "obj_" . $obj_id,
                        "txt"           => $title,
                        "default"       => true,
                        "no_permission" => $no_perm,
                        "path"          => $relpath,
                        "icon"          => $icon
                    ];
                }
            }

            if ($parent) {
                $columns["obj_" . $this->obj_id] = $parent;
            }
        }

        return $cols;
    }


    /**
     * @inheritdoc
     */
    protected function initData()/*: void*/
    {
        $this->setExternalSorting(false);
        $this->setExternalSegmentation(false);
        $this->setLimit(99999999999, 99999999999);
        $this->determineOffsetAndOrder(false);

        $filter = $this->getFilterValues2();

        $collection = ilTrQuery::getObjectIds($this->obj_id, $this->ref_id, true);
        if ($collection["object_ids"]) {
            // we need these for the timing warnings
            $this->ref_ids = $collection["ref_ids"];
            $additional_fields = $this->getStandardReportColumns();
            $additional_fields[] = 'status';

            $data = ilTrQuery::getUserObjectMatrix($this->ref_id, $collection["object_ids"], [], $additional_fields, $this->user_fields, false);

            // percentage export
            if ($data["set"]) {
                $this->perc_map = [];
                foreach ($data["set"] as $row_idx => $row) {
                    foreach ($row as $column => $value) {
                        if (substr($column, -5) == "_perc") {
                            $obj_id = explode("_", $column);
                            $obj_id = (int) $obj_id[1];

                            // #18673
                            if (!$this->isPercentageAvailable($obj_id)
                                || !(int) $value
                            ) {
                                unset($data["set"][$row_idx][$column]);
                            } else {
                                $this->perc_map[$obj_id] = true;
                            }
                        }
                    }
                }
            }

            //filter
            if ($filter['org_units'] > 0) {
                $employees = ilObjOrgUnitTree::_getInstance()->getEmployees($filter['org_units'], true);
                $superior = ilObjOrgUnitTree::_getInstance()->getSuperiors($filter['org_units'], true);

                $usr_ids_filtered_by_orgu = array_merge(array_values($employees), array_values($superior));
                $usr_ids_filtered_by_orgu = array_unique($usr_ids_filtered_by_orgu);
            }
            //print_r($data["set"]);exit;
            if ($filter['org_units'] > 0) {
                foreach ($data["set"] as $key => $usr_orgu) {
                    if (count($usr_ids_filtered_by_orgu) == 0 || !in_array($usr_orgu['usr_id'], $usr_ids_filtered_by_orgu)) {
                        unset($data["set"][$key]);
                    }
                }
            }
            unset($filter['org_units']);

            //TODO filter contains unusual values...
            unset($filter['gender']);
            $table_data = [];
            if (count($data["set"]) > 0) {
                foreach ($data["set"] as $row) {

                    $filtered = false;
                    foreach ($filter as $filter_field => $filter_vaue) {
                        if ((!empty($filter_vaue) || is_numeric($filter_vaue)) && $row[$filter_field] != $filter_vaue) {
                            $filtered = true;
                        }
                    }

                    if (!$filtered) {
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
     * @return array
     */
    function getStandardReportColumns() : array
    {
        $scol = [];
        foreach ($this->selected_column as $k => $v) {
            if (key_exists($k, parent::getSelectableColumns2())) {
                $scol[$k] = $k;
            }
        }

        return $scol;
    }


    /**
     * @param int $a_obj_id
     *
     * @return bool
     */
    protected function isPercentageAvailable(int $a_obj_id)
    {
        // TODO:
        $olp = ilObjectLP::getInstance($a_obj_id);
        $mode = $olp->getCurrentMode();
        if (in_array($mode, [
            ilLPObjSettings::LP_MODE_TLT,
            ilLPObjSettings::LP_MODE_VISITS,
            ilLPObjSettings::LP_MODE_SCORM,
            ilLPObjSettings::LP_MODE_VISITED_PAGES,
            ilLPObjSettings::LP_MODE_TEST_PASSED
        ])
        ) {
            return true;
        }

        return false;
    }


    /**
     * @param array $row
     */
    protected function fillRow(/*array*/
        $row
    )/*: void*/
    {
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
    protected function initId()/*: void*/
    {
        $this->setId('srlprep_matrix');
        $this->setPrefix('srlprep_matrix');
    }


    /**
     * @param ilExcel $excel
     * @param int     $row
     * @param array   $result
     */
    protected function fillRowExcel(
        ilExcel $excel, /*int*/
        &$row, /*array*/
        $result
    )/*: void*/
    {
        $col = 0;
        foreach ($this->getSelectableColumns2() as $column) {
            $excel->setCell($row, $col, $this->getColumnValue($column["id"], $result, true));
            $col++;
        }
    }


    /**
     * @inheritdoc
     */
    protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/
    {
        self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, $row["usr_id"]);
    }
}

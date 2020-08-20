<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilAdvancedSelectionListGUI;
use ilSrLpReportPlugin;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\LearningProgressPieUI\SrLpReport\LearningProgressPieUITrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractStaffTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractStaffTableGUI extends TableGUI
{

    use SrLpReportTrait;
    use LearningProgressPieUITrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    /**
     * @var bool
     */
    protected $actions = true;


    /**
     * @inheritdoc
     */
    protected function initExport()/*: void*/
    {
        $this->setExportFormats([self::EXPORT_EXCEL, self::EXPORT_CSV, self::EXPORT_PDF]);
    }


    /**
     * @inheritdoc
     */
    protected function fillRow(/*array*/
        $row
    )/*: void*/
    {
        parent::fillRow($row);

        if ($this->actions) {
            $actions = new ilAdvancedSelectionListGUI();
            $actions->setListTitle(self::dic()->language()->txt("actions"));
            $actions->setAsynch(true);
            $this->extendsActionsMenu($actions, $row);
            $actions->setAsynchUrl(str_replace("\\", "\\\\", self::dic()->ctrl()
                ->getLinkTarget($this->parent_obj, AbstractStaffGUI::CMD_GET_ACTIONS, "", true)));
            $this->tpl->setVariable("COLUMN", self::output()->getHTML($actions));
            $this->tpl->parseCurrentBlock();
        }
    }


    /**
     * @return string
     */
    public function getHTML() : string
    {
        self::dic()->mainTemplate()->setRightContent($this->getRightHTML());

        return parent::getHTML();
    }


    /**
     * @return array
     */
    public final function getFilterValues2() : array
    {
        $filter = $this->getFilterValues();

        if (isset($filter["status"])) {
            if ($filter["status"] > 0) {
                $filter["status"] -= 1;
            } else {
                unset($filter["status"]);
            }
        }

        if (isset($filter["lp_status"])) {
            if ($filter["lp_status"] > 0) {
                $filter["lp_status"] -= 1;
            } else {
                unset($filter["lp_status"]);
            }
        }

        return $filter;
    }


    /**
     * @param ilAdvancedSelectionListGUI $actions
     * @param array                      $row
     */
    protected abstract function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/
    ;


    /**
     * @return string
     */
    protected abstract function getRightHTML() : string;
}

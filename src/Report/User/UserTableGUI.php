<?php

namespace srag\Plugins\SrLpReport\Report\User;

use ilAdvancedSelectionListGUI;
use ilCourseParticipants;
use srag\Plugins\SrLpReport\Report\AbstractReport2TableGUI;
use srag\Plugins\SrLpReport\Report\Reports;

/**
 * Class UserTableGUI
 *
 * @package srag\Plugins\SrLpReport\Report\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserTableGUI extends AbstractReport2TableGUI
{

    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {
        $this->setId('srlprep_usrs');
        $this->setPrefix('srlprep_usrs');
    }


    /**
     * @inheritdoc
     */
    protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/
    {
        self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_USR_ID, $row["usr_id"]);
    }


    protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            default:
                return parent::getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT);
                break;
        }
    }
}

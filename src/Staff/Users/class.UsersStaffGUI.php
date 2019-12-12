<?php

namespace srag\Plugins\SrLpReport\Staff\Users;

use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;

/**
 * Class UsersStaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff\Users
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\Users\UsersStaffGUI: srag\Plugins\SrLpReport\Staff\StaffGUI
 */
class UsersStaffGUI extends AbstractStaffGUI
{

    const TAB_ID = "users";
    const CMD_SET_ORG_UNIT_FILTER = "setOrgUnitFilter";
    const ENABLE_CONFIG_KEY = Config::KEY_ENABLE_USERS_VIEW;


    /**
     * @inheritdoc
     */
    public function executeCommand()/*: void*/
    {
        parent::executeCommand();

        $cmd = self::dic()->ctrl()->getCmd();

        switch ($cmd) {
            case self::CMD_SET_ORG_UNIT_FILTER:
                $this->{$cmd}();
                break;

            default:
                break;
        }
    }


    /**
     * @inheritdoc
     */
    protected function setTabs()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function getTable(string $cmd = self::CMD_INDEX) : AbstractStaffTableGUI
    {
        $table = new UsersTableGUI($this, $cmd);

        return $table;
    }


    /**
     * @inheritdoc
     */
    protected function getActionsArray() : array
    {
        return self::ilias()->staff()->users()->getActionsArray();
    }


    /**
     *
     */
    protected function setOrgUnitFilter()/*: void*/
    {
        $org_unit_id = intval(filter_input(INPUT_GET, Reports::GET_PARAM_ORG_UNIT_ID));

        $table = $this->getTable(self::CMD_RESET_FILTER);
        $table->resetFilter();
        $table->resetOffset();

        $_POST["org_unit"] = $org_unit_id;
        $this->applyFilter();
    }
}

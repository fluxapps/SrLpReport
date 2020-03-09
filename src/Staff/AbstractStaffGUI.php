<?php

namespace srag\Plugins\SrLpReport\Staff;

use ILIAS\UI\Component\Button\Shy;
use ilSrLpReportPlugin;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\OrgUnitAjaxAutoCompleteCtrl;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractStaffGUI
 *
 * @package srag\Plugins\SrLpReport\Staff
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractStaffGUI
{

    use DICTrait;
    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const CMD_INDEX = "index";
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_RESET_FILTER = "resetFilter";
    const CMD_GET_ACTIONS = "getActions";
    /**
     * @var string
     *
     * @abstract
     */
    const TAB_ID = "";
    /**
     * @var string
     *
     * @abstract
     */
    const ENABLE_CONFIG_KEY = "";


    /**
     * AbstractStaffGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        if (!Config::getField(static::ENABLE_CONFIG_KEY)) {
            die();
        }

        $this->setTabs();

        self::dic()->tabs()->activateTab(static::TAB_ID);

        self::dic()->mainTemplate()->addCss(self::plugin()->directory() . "/css/srcrsreport.css");

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(OrgUnitAjaxAutoCompleteCtrl::class):
                self::dic()->ctrl()->forwardCommand(new OrgUnitAjaxAutoCompleteCtrl());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_INDEX);

                switch ($cmd) {
                    case self::CMD_INDEX:
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_RESET_FILTER:
                    case self::CMD_GET_ACTIONS:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function index()/*: void*/
    {
        $table = $this->getTable();

        self::output()->output($table, true);
    }


    /**
     *
     */
    protected function applyFilter()/*: void*/
    {
        $table = $this->getTable(self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this);
        $this->index(); // Fix reset offset
    }


    /**
     *
     */
    protected function resetFilter()/*: void*/
    {
        $table = $this->getTable(self::CMD_RESET_FILTER);

        $table->resetFilter();

        $table->resetOffset();

        //self::dic()->ctrl()->redirect($this);
        $this->index(); // Fix reset offset
    }


    /**
     *
     */
    protected function getActions()/*: void*/
    {
        self::output()->output(array_map(function (Shy $button) : string {
            return self::output()->getHTML([
                "<li>",
                $button,
                "</li>"
            ]);
        }, $this->getActionsArray()));
    }


    /**
     *
     */
    protected abstract function setTabs()/*: void*/
    ;


    /**
     * @param string $cmd
     *
     * @return AbstractStaffTableGUI
     */
    protected abstract function getTable(string $cmd = self::CMD_INDEX) : AbstractStaffTableGUI;


    /**
     * @return Shy[]
     */
    protected abstract function getActionsArray() : array;
}

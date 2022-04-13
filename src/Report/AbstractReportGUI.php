<?php

namespace srag\Plugins\SrLpReport\Report;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Component\Button\Shy;
use ilObject;
use ilSrLpReportPlugin;
use ilTemplateException;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractReportGUI
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractReportGUI
{

    use DICTrait;
    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const CMD_INDEX = "index";
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_RESET_FILTER = "resetFilter";
    const CMD_GET_ACTIONS = "getActions";
    const CMD_MAIL_SELECTED_USERS = 'mailselectedusers';
    /**
     * @var string
     *
     * @abstract
     */
    const TAB_ID = "";


    /**
     * AbstractReportGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        self::dic()->tabs()->activateSubTab(static::TAB_ID);

        $this->initGUI();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
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
        }
    }


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    protected function initGUI()/*: void*/
    {
        self::dic()->ui()->mainTemplate()->addCss(self::plugin()->directory() . "/css/srcrsreport.css");

        self::dic()->ui()->mainTemplate()->setTitleIcon(ilObject::_getIcon("", "tiny", self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()
            ->lookupObjId(self::reports()->getReportObjRefId()))));

        self::dic()->ui()->mainTemplate()->setTitle(self::dic()->language()->txt("learning_progress") . " " . self::dic()->objDataCache()
                ->lookupTitle(self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId())));
    }


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    protected function index()/*: void*/
    {
        self::output()->output($this->getTable(), true);
    }


    /**
     * @throws DICException
     * @throws ilTemplateException
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
     * @throws DICException
     * @throws ilTemplateException
     */
    protected function resetFilter()/*: void*/
    {
        $table = $this->getTable(self::CMD_RESET_FILTER);

        $table->resetOffset();

        $table->resetFilter();

        //self::dic()->ctrl()->redirect($this);
        $this->index(); // Fix reset offset
    }


    /**
     *
     */
    protected function getActions()/*: void*/
    {
        self::output()->output(array_map(function (Component $button) : string {
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
    protected abstract function setTabs()/*: void*/ ;


    /**
     * @param string $cmd
     *
     * @return AbstractReportTableGUI
     */
    protected abstract function getTable(string $cmd = self::CMD_INDEX) : AbstractReportTableGUI;


    /**
     * @return Shy[]
     */
    protected abstract function getActionsArray() : array;
}

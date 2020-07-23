<?php

namespace srag\Plugins\SrLpReport\Report\ConfigPerObject;

use ilLink;
use ilSrLpReportPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ConfigPerObjectGUI
 *
 * @package           srag\Plugins\SrLpReport\Report\ConfigPerObject
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\ConfigPerObject\ConfigPerObjectGUI: ilUIPluginRouterGUI
 */
class ConfigPerObjectGUI
{

    use DICTrait;
    use SrLpReportTrait;

    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const CMD_BACK = "back";
    const CMD_EDIT_CONFIG_PER_OBJECT = "editConfigPerObject";
    const CMD_UPDATE_CONFIG_PER_OBJECT = "updateConfigPerObject";
    const TAB_CONFIG_PER_OBJECT = "config_per_object";


    /**
     * ConfigPerObjectGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand()/* : void*/
    {
        if (!self::reports()->configPerObjects()->isEnableReportingView() || !self::dic()->access()->checkAccess("write", "", self::reports()->getReportObjRefId())) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, Reports::GET_PARAM_REF_ID);

        self::dic()->tabs()->clearTargets();
        self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));
        self::dic()->tabs()->addTab(self::TAB_CONFIG_PER_OBJECT, ilSrLpReportPlugin::PLUGIN_NAME,
            self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_CONFIG_PER_OBJECT));

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_EDIT_CONFIG_PER_OBJECT:
                    case self::CMD_UPDATE_CONFIG_PER_OBJECT:
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
    protected function back()/* : void*/
    {
        self::dic()->ctrl()->redirectToURL(ilLink::_getLink(self::reports()->getReportObjRefId()));
    }


    /**
     *
     */
    protected function editConfigPerObject()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIG_PER_OBJECT);

        $form = new ConfigPerObjectFormGUI($this, self::reports()->configPerObjects()->getConfigPerObject(self::reports()->getReportObjRefId()));

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateConfigPerObject()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIG_PER_OBJECT);

        $form = new ConfigPerObjectFormGUI($this, self::reports()->configPerObjects()->getConfigPerObject(self::reports()->getReportObjRefId()));

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::dic()->language()->txt("settings_saved"), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_CONFIG_PER_OBJECT);
    }
}

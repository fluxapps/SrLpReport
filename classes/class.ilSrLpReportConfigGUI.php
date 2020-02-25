<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\ActiveRecordConfig\SrLpReport\ActiveRecordConfigGUI;
use srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\ObjectsAjaxAutoCompleteCtrl;
use srag\Plugins\SrLpReport\Config\ConfigFormGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ilSrLpReportConfigGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\CustomInputGUIs\SrLpReport\MultiSelectSearchNewInputGUI\ObjectsAjaxAutoCompleteCtrl: ilSrLpReportConfigGUI
 */
class ilSrLpReportConfigGUI extends ActiveRecordConfigGUI
{

    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    /**
     * @var array
     */
    protected static $tabs = [self::TAB_CONFIGURATION => ConfigFormGUI::class];


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/*: void*/
    {
        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ObjectsAjaxAutoCompleteCtrl::class):
                self::dic()->ctrl()->forwardCommand(new ObjectsAjaxAutoCompleteCtrl("crs"));
                break;

            default:
                parent::performCommand($cmd);
                break;
        }
    }
}

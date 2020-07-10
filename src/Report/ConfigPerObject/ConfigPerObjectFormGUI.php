<?php

namespace srag\Plugins\SrLpReport\Report\ConfigPerObject;

use ilCheckboxInputGUI;
use ilSrLpReportPlugin;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Config\ConfigFormGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ConfigPerObjectFormGUI
 *
 * @package srag\Plugins\SrLpReport\Report\ConfigPerObject
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ConfigPerObjectFormGUI extends PropertyFormGUI
{

    use SrLpReportTrait;

    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    /**
     * @var ConfigPerObject
     */
    protected $config_per_object;


    /**
     * ConfigPerObjectFormGUI constructor
     *
     * @param ConfigPerObjectGUI $parent
     * @param ConfigPerObject    $config_per_object
     */
    public function __construct(ConfigPerObjectGUI $parent, ConfigPerObject $config_per_object)
    {
        $this->config_per_object = $config_per_object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return Items::getter($this->config_per_object, $key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ConfigPerObjectGUI::CMD_UPDATE_CONFIG_PER_OBJECT, self::dic()->language()->txt("save"));
        $this->addCommandButton(ConfigPerObjectGUI::CMD_BACK, self::dic()->language()->txt("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "enabled" => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                "setTitle"           => self::plugin()->translate("reporting", ConfigFormGUI::LANG_MODULE)
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                Items::setter($this->config_per_object, $key, $value);
                break;
        }
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (!parent::storeForm()) {
            return false;
        }

        self::reports()->configPerObjects()->storeConfigPerObject($this->config_per_object);

        return true;
    }
}

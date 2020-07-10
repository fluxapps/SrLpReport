<?php

namespace srag\Plugins\SrLpReport\Report\ConfigPerObject;

use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ConfigPerObjects
 *
 * @package srag\Plugins\SrLpReport\Report\ConfigPerObject
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ConfigPerObjects
{

    use DICTrait;
    use SrLpReportTrait;

    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * ConfigPerObjects constructor
     */
    private function __construct()
    {

    }


    /**
     * @param int $obj_ref_id
     *
     * @return ConfigPerObject
     */
    public function getConfigPerObject(int $obj_ref_id) : ConfigPerObject
    {
        /**
         * @var ConfigPerObject|null $config_per_object
         */

        $obj_id = self::dic()->objDataCache()->lookupObjId($obj_ref_id);

        $config_per_object = ConfigPerObject::where(["obj_id" => $obj_id])->first();

        if ($config_per_object === null) {
            $config_per_object = new ConfigPerObject();

            $config_per_object->setObjId($obj_id);

            $config_per_object->setEnabled(Config::getField(Config::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT_NEW_OBJECTS));

            $this->storeConfigPerObject($config_per_object);
        }

        return $config_per_object;
    }


    /**
     * @param int|null $obj_ref_id
     *
     * @return bool
     */
    public function isEnableReportingView(/*?*/ int $obj_ref_id = null) : bool
    {
        if (!Config::getField(Config::KEY_ENABLE_REPORTING_VIEW)) {
            return false;
        }

        if (!empty($obj_ref_id)) {
            if (!Config::getField(Config::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT)) {
                return true;
            }

            if ($this->getConfigPerObject($obj_ref_id)->isEnabled()) {
                return true;
            }
        } else {
            if (Config::getField(Config::KEY_ENABLE_REPORTING_VIEW_PER_OBJECT)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param ConfigPerObject $config_per_object
     */
    public function storeConfigPerObject(ConfigPerObject $config_per_object)/* : void*/
    {
        $config_per_object->store();
    }
}

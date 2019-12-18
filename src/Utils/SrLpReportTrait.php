<?php

namespace srag\Plugins\SrLpReport\Utils;

use srag\Plugins\SrLpReport\Access\Access;
use srag\Plugins\SrLpReport\Access\Ilias;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Tab\TabGUI;

/**
 * Trait SrLpReportTrait
 *
 * @package srag\Plugins\SrLpReport\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait SrLpReportTrait
{

    /**
     * @return Access
     */
    protected static function access() : Access
    {
        return Access::getInstance();
    }


    /**
     * @return Ilias
     */
    protected static function ilias() : Ilias
    {
        return Ilias::getInstance();
    }


    /**
     * @return Reports
     */
    protected static function reports() : Reports
    {
        return Reports::getInstance();
    }
}

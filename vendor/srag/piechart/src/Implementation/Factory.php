<?php

namespace srag\PieChart\SrLpReport\Implementation;

use srag\PieChart\SrLpReport\Component\Factory as FactoryInterface;
use srag\PieChart\SrLpReport\Component\PieChart as PieChartInterface;

/**
 * Class Factory
 *
 * @package srag\PieChart\SrLpReport\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function pieChart(array $pieChartItems) : PieChartInterface
    {
        return new PieChart($pieChartItems);
    }
}

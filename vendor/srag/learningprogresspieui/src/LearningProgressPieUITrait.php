<?php

namespace srag\LearningProgressPieUI\SrLpReport;

/**
 * Trait LearningProgressPieUITrait
 *
 * @package srag\LearningProgressPieUI\SrLpReport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait LearningProgressPieUITrait
{

    /**
     * @return Factory
     */
    protected static function learningProgressPieUI() : Factory
    {
        return Factory::getInstance();
    }
}

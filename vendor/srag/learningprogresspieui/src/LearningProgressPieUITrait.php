<?php

namespace srag\LearningProgressPieUI\SrLpReport;

/**
 * Trait LearningProgressPieUITrait
 *
 * @package srag\LearningProgressPieUI\SrLpReport
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

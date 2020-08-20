<?php

namespace srag\LearningProgressPieUI\SrLpReport;

use srag\DIC\SrLpReport\DICTrait;

/**
 * Class Factory
 *
 * @package srag\LearningProgressPieUI\SrLpReport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory
{

    use DICTrait;

    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


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
     * @return CountLearningProgressPieUI
     */
    public function count() : CountLearningProgressPieUI
    {
        return new CountLearningProgressPieUI();
    }


    /**
     * @return ObjIdsLearningProgressPieUI
     */
    public function objIds() : ObjIdsLearningProgressPieUI
    {
        return new ObjIdsLearningProgressPieUI();
    }


    /**
     * @return UsrIdsLearningProgressPieUI
     */
    public function usrIds() : UsrIdsLearningProgressPieUI
    {
        return new UsrIdsLearningProgressPieUI();
    }
}

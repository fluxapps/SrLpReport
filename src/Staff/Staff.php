<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Staff\CourseAdministration\CourseAdministration;
use srag\Plugins\SrLpReport\Staff\Courses\Courses;
use srag\Plugins\SrLpReport\Staff\User\User;
use srag\Plugins\SrLpReport\Staff\Users\Users;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Staff
 *
 * @package srag\Plugins\SrLpReport\Staff
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Staff
{

    use DICTrait;
    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    /**
     * @var self
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
     * Staff constructor
     */
    private function __construct()
    {

    }


    /**
     * @return Courses
     */
    public function courses() : Courses
    {
        return Courses::getInstance();
    }


    /**
     * @return CourseAdministration
     */
    public function courseAdministration() : CourseAdministration
    {
        return CourseAdministration::getInstance();
    }


    /**
     * @return User
     */
    public function user() : User
    {
        return User::getInstance();
    }


    /**
     * @return Users
     */
    public function users() : Users
    {
        return Users::getInstance();
    }
}

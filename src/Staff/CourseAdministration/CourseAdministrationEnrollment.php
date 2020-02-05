<?php

namespace srag\Plugins\SrLpReport\Staff\CourseAdministration;

use ActiveRecord;
use arConnector;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class CourseAdministrationEnrollment
 *
 * @package srag\Plugins\SrLpReport\Staff\CourseAdministration
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CourseAdministrationEnrollment extends ActiveRecord
{

    use DICTrait;
    use SrLpReportTrait;
    const TABLE_NAME = "srlprep_crs_adm_enr";
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * @return string
     */
    public function getConnectorContainerName()
    {
        return self::TABLE_NAME;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public static function returnDbTableName()
    {
        return self::TABLE_NAME;
    }


    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $crs_obj_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $usr_id;
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $enrollment_time = null;
    /**
     * @var int|null
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $signedout_time = null;

    /**
     * CourseAdministrationEnrollment constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getCrsObjId() : int
    {
        return $this->crs_obj_id;
    }


    /**
     * @param int $crs_obj_id
     */
    public function setCrsObjId(int $crs_obj_id)
    {
        $this->crs_obj_id = $crs_obj_id;
    }


    /**
     * @return int
     */
    public function getUsrId() : int
    {
        return $this->usr_id;
    }


    /**
     * @param int $usr_id
     */
    public function setUsrId(int $usr_id)
    {
        $this->usr_id = $usr_id;
    }


    /**
     * @return int|null
     */
    public function getEnrollmentTime()/* : ?int*/
    {
        return $this->enrollment_time;
    }


    /**
     * @param int|null $enrollment_time
     */
    public function setEnrollmentTime(/*?*/int $enrollment_time = null)/* : void*/
    {
        $this->enrollment_time = $enrollment_time;
    }


    /**
     * @return int|null
     */
    public function getSignedoutTime()/* : ?int*/
    {
        return $this->signedout_time;
    }


    /**
     * @param int|null $signedout_time
     */
    public function setSignedoutTime(/*?*/int $signedout_time = null)/* : void*/
    {
        $this->signedout_time = $signedout_time;
    }
}

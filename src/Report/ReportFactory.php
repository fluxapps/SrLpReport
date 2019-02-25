<?php

namespace srag\Plugins\SrLpReport\Report;

use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ReportFactory
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ReportFactory {

	use SrLpReportTrait;
	use DICTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const GET_PARAM_REF_ID = "ref_id";
	const GET_PARAM_TARGET = "target";
	/**
	 * @var self[]
	 */
	protected static $instances = [];
	const REPORT_OBJECT_TYPE_SINGLE = 1;
	const REPORT_OBJECT_TYPE_ALL = 2;
	const REPORT_USER_TYPE_SINGLE = 1;
	const REPORT_USER_TYPE_ALL = 2;
	const REPORT_VIEW_TYPE_LIST = 1;
	const REPORT_VIEW_TYPE_MATRIX = 2;
	const REPORT_VIEW_TYPE_SUMMARY = 2;
	/**
	 * @var int
	 */
	protected $report_object_type;
	/**
	 * @var int
	 */
	protected $report_user_type;
	/**
	 * @var int
	 */
	protected $report_view_type;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * ReportFactory constructor
	 */
	private function __construct() {

	}


	/**
	 * @return int
	 */
	public static function getReportObjRefId(): int {
		$obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

		if ($obj_ref_id === NULL) {
			$param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

			$obj_ref_id = explode("_", $param_target)[1];
		}

		$obj_ref_id = intval($obj_ref_id);

		if ($obj_ref_id > 0) {
			return $obj_ref_id;
		} else {
			return NULL;
		}
	}
}

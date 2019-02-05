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
		if ((int)$_GET['ref_id']) {
			return $_GET['ref_id'];
		}
		$target_arr = explode('_', (string)$_GET['target']);
		if (isset($target_arr[1]) and (int)$target_arr[1]) {
			return $target_arr[1];
		}
		if ((int)$_GET['details_id']) {
			return $_GET['details_id'];
		}

		return 0;
	}
}

<?php

namespace srag\Plugins\SrLpReport\Report;


use ilSrReportPlugin;
use MatrixSingleObjectSingleUserGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use MatrixGUI;
use SingleObjectAllUserGUI;
use ilObject;

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
	const PLUGIN_CLASS_NAME = ilSrReportPlugin::class;
	/**
	 * @var self[]
	 */
	protected static $instances = [];

	const REPORT_OBJECT_TYPE_SINGLE = 1;
	const REPORT_OBJECT_TYPE_ALL = 2;

	const REPORT_USER_TYPE_SINGLE = 1;
	const REPORT_USER_TYPE_ALL = 2;

	const REPORT_VIEW_TYPE_LIST= 1;
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
	 * @param int $obj_ref_id
	 * @param int $user_id
	 * @param int $report_view_type
	 *
	 * @return ReportInterface
	 */
	public function buildReportRefIdUserId(int $obj_ref_id, int $user_id, int $report_view_type):ReportInterface {

		switch($report_view_type) {
			case self::REPORT_VIEW_TYPE_MATRIX:
				return ReportMatrixSingleObjectSingleUser::getInstance($obj_ref_id,$user_id);
				break;
		}
	}

	/**
	 * @param int $obj_ref_id
	 * @param int $user_id
	 * @param int $report_view_type
	 *
	 * @return ReportInterface
	 */
	public function buildReportByClassName(string $class_name):ReportInterface {


		switch(strtolower($class_name)) {
			case strtolower(MatrixSingleObjectSingleUserGUI::class):
				return ReportMatrixSingleObjectSingleUser::getInstance(self::getReportObjRefId(),$this->getReportUsrId());
				break;
			case  strtolower(SingleObjectAllUserGUI::class):
				return ReportListSingleObjectAllUser::getInstance(self::getReportObjRefId());
				break;
		}
	}


	/**
	 * @return int
	 */
	public static function getReportObjRefId():int {
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

	/**
	 * @return int
	 */
	private function getReportUsrId() {
		return intval(filter_input(INPUT_GET, "usr_id"));
	}
}

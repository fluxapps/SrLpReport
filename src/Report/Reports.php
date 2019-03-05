<?php

namespace srag\Plugins\SrLpReport\Report;

use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Reports
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Reports {

	use SrLpReportTrait;
	use DICTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const GET_PARAM_REF_ID = "ref_id";
	const GET_PARAM_USR_ID = "usr_id";
	const GET_PARAM_TARGET = "target";
	const GET_PARAM_RETURN = "return";
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
	 * Reports constructor
	 */
	private function __construct() {

	}


	/**
	 * @return int|null
	 */
	public function getReportObjRefId()/*: ?int*/ {
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


	/**
	 * @return int|null
	 */
	public function getUsrId()/*: ?int*/ {
		$usr_id = filter_input(INPUT_GET, self::GET_PARAM_USR_ID);

		$usr_id = intval($usr_id);

		if ($usr_id > 0) {
			return $usr_id;
		} else {
			return NULL;
		}
	}
}

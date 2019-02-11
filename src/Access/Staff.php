<?php

namespace srag\Plugins\SrLpReport\Access;

use ilMStListUsers;
use ilMyStaffAccess;
use ilOrgUnitPathStorage;
use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Staff
 *
 * @package srag\Plugins\SrLpReport\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Staff {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
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
	 * Staff constructor
	 */
	private function __construct() {

	}


	/**
	 * @param int    $usr_id
	 * @param array  $filter
	 * @param string $order
	 * @param string $order_direction
	 * @param int    $limit_start
	 * @param int    $limit_end
	 *
	 * @return array
	 */
	public function getData(int $usr_id, array $filter, string $order, string $order_direction, int $limit_start, int $limit_end): array {
		$data = [];

		$arr_usr_id = (new ilMyStaffAccess())->getUsersForUser($usr_id);

		$options = [
			"filters" => $filter,
			"limit" => [],
			"count" => true,
			"sort" => [
				"field" => $order,
				"direction" => $order_direction,
			]
		];

		$data["max_count"] = ilMStListUsers::getData($arr_usr_id, $options);

		$options["limit"] = [
			"start" => $limit_start,
			"end" => $limit_end,
		];
		$options["count"] = false;

		$data["data"] = ilMStListUsers::getData($arr_usr_id, $options);

		return $data;
	}


	/**
	 * @return array
	 */
	public function getOrgUnits(): array {
		$where = ilOrgUnitPathStorage::orderBy("path");

		$paths = $where->getArray("ref_id", "path");

		return $paths;
	}
}

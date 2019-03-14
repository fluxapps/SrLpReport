<?php

namespace srag\Plugins\SrLpReport\Staff\User;

use Closure;
use ilAdvancedSelectionListGUI;
use ilMStListCourse;
use ilMStShowUserCourses;
use ilMyStaffAccess;
use ilOrgUnitOperation;
use ilSrLpReportPlugin;
use ilSrLpReportUIHookGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class User
 *
 * @package srag\Plugins\SrLpReport\Staff\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class User {

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
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * User constructor
	 */
	private function __construct() {

	}


	/**
	 * @param int    $user_id
	 * @param array  $filter
	 * @param string $order
	 * @param string $order_direction
	 * @param int    $limit_start
	 * @param int    $limit_end
	 *
	 * @return array
	 */
	public function getData(int $user_id, array $filter, string $order, string $order_direction, int $limit_start, int $limit_end): array {
		$data = [];

		$options = [
			"filters" => $filter,
			"limit" => [],
			"count" => true,
			"sort" => [
				"field" => $order,
				"direction" => $order_direction,
			]
		];

		$users = ilMyStaffAccess::getInstance()->getUsersForUserOperationAndContext(self::dic()->user()
			->getId(), ilOrgUnitOperation::OP_ACCESS_ENROLMENTS, ilSrLpReportUIHookGUI::TYPE_CRS);

		$options["filters"]["usr_id"] = $user_id;

		$data["max_count"] = ilMStShowUserCourses::getData($users, $options);

		$options["limit"] = [
			"start" => $limit_start,
			"end" => $limit_end,
		];
		$options["count"] = false;

		$data["data"] = array_map(function (ilMStListCourse $course): array {
			$vars = Closure::bind(function (): array {
				$vars = get_object_vars($this);

				$vars["usr_obj"] = $this->returnIlUserObj();
				$vars["crs_obj"] = $this->returnIlCourseObj();

				return $vars;
			}, $course, ilMStListCourse::class)();

			$vars["learning_progress_courses"] = array_map(function (array $child): int {
				return intval($child["child"]);
			}, self::dic()->tree()->getChilds($vars["crs_ref_id"]));

			return $vars;
		}, ilMStShowUserCourses::getData($users, $options));

		return $data;
	}


	/**
	 * @param ilAdvancedSelectionListGUI $actions
	 */
	public function fillActions(ilAdvancedSelectionListGUI $actions) {

	}
}

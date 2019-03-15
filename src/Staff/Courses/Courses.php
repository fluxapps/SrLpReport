<?php

namespace srag\Plugins\SrLpReport\Staff\Courses;

use Closure;
use ilAdvancedSelectionListGUI;
use ilMStListCourse;
use ilMStListCourses;
use ilMyStaffAccess;
use ilSrLpReportPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Report\ReportGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Report\User\UserReportGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Courses
 *
 * @package srag\Plugins\SrLpReport\Staff\Courses
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Courses {

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
	 * Courses constructor
	 */
	private function __construct() {

	}


	/**
	 * @param array  $filter
	 * @param string $order
	 * @param string $order_direction
	 * @param int    $limit_start
	 * @param int    $limit_end
	 *
	 * @return array
	 */
	public function getData(array $filter, string $order, string $order_direction, int $limit_start, int $limit_end): array {
		$data = [];

		$users = ilMyStaffAccess::getInstance()->getUsersForUser(self::dic()->user()->getId());

		$options = [
			"filters" => $filter,
			"limit" => [],
			"count" => true,
			"sort" => [
				"field" => $order,
				"direction" => $order_direction
			]
		];

		$data["max_count"] = ilMStListCourses::getData($users, $options);

		$options["limit"] = [
			"start" => $limit_start,
			"end" => $limit_end
		];
		$options["count"] = false;

		$data_ = array_map(function (ilMStListCourse $course): array {
			$vars = Closure::bind(function (): array {
				$vars = get_object_vars($this);

				$vars["usr_obj"] = $this->returnIlUserObj();
				$vars["crs_obj"] = $this->returnIlCourseObj();

				return $vars;
			}, $course, ilMStListCourse::class)();

			$vars["crs_obj_id"] = self::dic()->objDataCache()->lookupObjId($vars["crs_ref_id"]);

			return $vars;
		}, ilMStListCourses::getData($users, $options));

		$data["data"] = array_map(function (array $course) use ($data_): array {
			$course["learning_progress_users"] = array_reduce(array_filter($data_, function (array $course_) use ($course): bool {
				return ($course_["crs_ref_id"] === $course["crs_ref_id"]);
			}), function (array $users, array $course): array {
				$users[] = intval($course["usr_id"]);

				return $users;
			}, []);

			return $course;
		}, array_reduce($data_, function (array $data, array $course): array {
			$data[$course["crs_ref_id"]] = $course;

			return $data;
		}, []));

		return $data;
	}


	/**
	 * @param ilAdvancedSelectionListGUI $actions
	 */
	public function fillActions(ilAdvancedSelectionListGUI $actions) {
		self::dic()->ctrl()->saveParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID);
		self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_RETURN, CoursesStaffGUI::class);

		$actions->addItem(self::dic()->language()->txt("learning_progress"), "", self::dic()->ctrl()->getLinkTargetByClass([
			ilUIPluginRouterGUI::class,
			ReportGUI::class,
			UserReportGUI::class
		]));
	}
}

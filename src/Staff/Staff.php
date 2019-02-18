<?php

namespace srag\Plugins\SrLpReport\Staff;

use Closure;
use ilMStListCourse;
use ilMStListCourses;
use ilMStListUser;
use ilMStListUsers;
use ilMyStaffAccess;
use ilOrgUnitOperation;
use ilOrgUnitOperationQueries;
use ilOrgUnitPathStorage;
use ilSrLpReportPlugin;
use ilUserSearchOptions;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Staff
 *
 * @package srag\Plugins\SrLpReport\Staff
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
	 * @return array
	 */
	public function getColumns(): array {
		return ilUserSearchOptions::getSelectableColumnInfo();
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
		self::dic()->language()->loadLanguageModule("trac");

		$data = [];

		$arr_usr_id = ilMyStaffAccess::getInstance()->getUsersForUser($usr_id);

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

		$data["data"] = array_map(function (ilMStListUser $user): array {
			$vars = Closure::bind(function (): array {
				$vars = get_object_vars($this);

				$vars["usr_obj"] = $this->returnIlUserObj();

				return $vars;
			}, $user, ilMStListUser::class)();

			$vars["org_units"] = ilOrgUnitPathStorage::getTextRepresentationOfUsersOrgUnits($vars["usr_id"]);

			$vars["interests_general"] = $vars["usr_obj"]->getGeneralInterestsAsText();

			$vars["interests_help_offered"] = $vars["usr_obj"]->getOfferingHelpAsText();

			ilMyStaffAccess::getInstance()->buildTempTableIlobjectsUserMatrixForUserOperationAndContext(self::dic()->user()
				->getId(), ilOrgUnitOperationQueries::findByOperationString(ilOrgUnitOperation::OP_ACCESS_ENROLMENTS, "crs")
				->getOperationId(), "crs");

			$vars["learning_progress_courses"] = array_map(function (ilMStListCourse $course): int {
				return self::dic()->objDataCache()->lookupObjId($course->getCrsRefId());
			}, ilMStListCourses::getData([ $vars["usr_id"] ]));

			return $vars;
		}, ilMStListUsers::getData($arr_usr_id, $options) ?: []);

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

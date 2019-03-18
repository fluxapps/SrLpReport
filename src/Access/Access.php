<?php

namespace srag\Plugins\SrLpReport\Access;

use ilLearningProgressAccess;
use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Access
 *
 * @package srag\Plugins\SrLpReport\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Access {

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
	 * Access constructor
	 */
	private function __construct() {

	}


	/**
	 * @param int $ref_id
	 *
	 * @return bool
	 */
	public function hasLPReadAccess(int $ref_id): bool {
		return ilLearningProgressAccess::checkPermission("read_learning_progress", $ref_id);
	}


	/**
	 * @param int $ref_id
	 *
	 * @return bool
	 */
	public function hasLPWriteAccess(int $ref_id): bool {
		return ilLearningProgressAccess::checkPermission("edit_learning_progress", $ref_id);
	}
}

<?php

namespace srag\Plugins\SrLpReport\Access;

use ilDBConstants;
use ilSrLpReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Staff\Staff;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Ilias
 *
 * @package srag\Plugins\SrLpReport\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Ilias {

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
	 * Ilias constructor
	 */
	private function __construct() {

	}


    /**
     * @param string|null $search
     *
     * @return array
     */
    public function searchCourses(/*?*/ string $search = null): array {
        $result = self::dic()->database()->queryF('SELECT obj_id, title FROM object_data WHERE type=%s' . (!empty($search) ? ' AND ' . self::dic()
                    ->database()
                    ->like("title", ilDBConstants::T_TEXT, '%%' . $search . '%%') : '') . ' ORDER BY title ASC', [ilDBConstants::T_TEXT], ["crs"]);

        $array = [];

        while (($row = $result->fetchAssoc()) !== false) {
            $array[$row["obj_id"]] = $row["title"];
        }

        return $array;
    }

	/**
	 * @return Staff
	 */
	public static function staff(): Staff {
		return Staff::getInstance();
	}
}

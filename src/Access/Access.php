<?php

namespace srag\Plugins\SrLpReport\Access;

use ilLearningProgressAccess;
use ilMyStaffAccess;
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

    const ROLE_OBJECT_ID_ADMINISTRATOR = 2;
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

	public function hasCurrentUserAccessToMyStaff() {
        global $DIC;

        if($DIC->rbac()->review()->isAssigned($DIC->user()->getId(),self::ROLE_OBJECT_ID_ADMINISTRATOR)) {
            return true;
        }

        return ilMyStaffAccess::getInstance()->hasCurrentUserAccessToMyStaff();
    }

    public function getUsersForUser(int $usr_id) {
        global $DIC;

        if($DIC->rbac()->review()->isAssigned($DIC->user()->getId(),self::ROLE_OBJECT_ID_ADMINISTRATOR)) {
            $q = "SELECT usr_id FROM usr_data;";
            $user_set  = $DIC->database()->query($q);
            while ($rec = $DIC->database()->fetchAssoc($user_set)) {
                $arr_users[$rec['usr_id']] = $rec['usr_id'];
            }
            return $arr_users;
        }

        return ilMyStaffAccess::getInstance()->getUsersForUser($usr_id);
    }

    public function getUsersForUserOperationAndContext($user_id, $org_unit_operation_string = ilMyStaffAccess::DEFAULT_ORG_UNIT_OPERATION, $context = ilMyStaffAccess::DEFAULT_CONTEXT, $tmp_table_name_prefix = ilMyStaffAccess::TMP_DEFAULT_TABLE_NAME_PREFIX_IL_OBJ_USER_MATRIX) {
	    global $DIC;

        if($DIC->rbac()->review()->isAssigned($DIC->user()->getId(),self::ROLE_OBJECT_ID_ADMINISTRATOR)) {
            $q = "SELECT usr_id FROM usr_data;";
            $user_set  = $DIC->database()->query($q);
            while ($rec = $DIC->database()->fetchAssoc($user_set)) {
                $arr_users[$rec['usr_id']] = $rec['usr_id'];
            }
            return $arr_users;
        }

        return ilMyStaffAccess::getInstance()->getUsersForUserOperationAndContext($user_id, $org_unit_operation_string, $context, $tmp_table_name_prefix);
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

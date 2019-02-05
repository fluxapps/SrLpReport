<?php

namespace srag\Plugins\SrLpReport\Report;

use ilSrLpReportPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\User\UserGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ReportListSingleObjectSingleUser
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ReportListSingleObjectSingleUser implements ReportInterface {

	use SrLpReportTrait;
	use DICTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const CLASS_PLUGIN_BASE_GUI = BaseGUI::class;
	const CLASS_GUI = UserGUI::class;
	const CLASS_PATH_ARRAY = [ ilUIPluginRouterGUI::class, self::CLASS_PLUGIN_BASE_GUI, self::CLASS_GUI ];
	/**
	 * @var self[]
	 */
	protected static $instances = [];
	/**
	 * @var int
	 */
	protected $rep_obj_type = ReportFactory::REPORT_OBJECT_TYPE_SINGLE;
	/**
	 * @var int
	 */
	protected $rep_user_type = ReportFactory::REPORT_USER_TYPE_SINGLE;
	/**
	 * @var int
	 */
	protected $obj_ref_id = 0;
	/**
	 * @var int
	 */
	protected $usr_id = 0;


	/**
	 * ReportListSingleObjectSingleUser constructor
	 *
	 * @param int $obj_ref_id
	 * @param int $usr_id
	 */
	private function __construct(int $obj_ref_id, int $usr_id) {
		$this->obj_ref_id = $obj_ref_id;
		$this->usr_id = $usr_id;

		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI, 'ref_id', $this->getObjRefId());
		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI, 'usr_id', $this->getUsrId());
		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI, 'sr_rp', 1);
	}


	/**
	 * @param int $usr_id
	 *
	 * @return self
	 */
	public static function getInstance(int $obj_ref_id, int $usr_id): self {
		if (!isset(self::$instances[$obj_ref_id . "-" . $usr_id])) {
			self::$instances[$obj_ref_id . "-" . $usr_id] = new self($obj_ref_id, $usr_id);
		}

		return self::$instances[$obj_ref_id . "-" . $usr_id];
	}


	/**
	 * @return string
	 */
	public function getLinkTarget(): string {
		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI, 'rep_obj_type', $this->getRepObjType());
		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI, 'rep_obj_type', $this->getRepUserType());

		return self::dic()->ctrl()->getLinkTargetByClass(self::CLASS_PATH_ARRAY);
	}


	/**
	 * @return UserGUI
	 */
	public function getGuiObject(): UserGUI {
		return new UserGUI();
	}


	/**
	 * @return int
	 */
	public function getObjRefId(): int {
		return $this->obj_ref_id;
	}


	/**
	 * @return int
	 */
	public function getUsrId(): int {
		return $this->usr_id;
	}


	/**
	 * @return int
	 */
	public function getRepObjType(): int {
		return $this->rep_obj_type;
	}


	/**
	 * @param int $rep_obj_type
	 */
	public function setRepObjType(int $rep_obj_type) {
		$this->rep_obj_type = $rep_obj_type;
	}


	/**
	 * @return int
	 */
	public function getRepUserType(): int {
		return $this->rep_user_type;
	}


	/**
	 * @param int $rep_user_type
	 */
	public function setRepUserType(int $rep_user_type) {
		$this->rep_user_type = $rep_user_type;
	}
}

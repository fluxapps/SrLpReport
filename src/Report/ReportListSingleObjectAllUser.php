<?php

namespace srag\Plugins\SrLpReport\Report;

use ilSrReportPlugin;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use SingleObjectAllUserGUI;
use ilSrLpReportGUI;

/**
 * Class ReportListSingleObjectAllUser
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ReportListSingleObjectAllUser implements ReportInterface {

	use SrLpReportTrait;
	use DICTrait;
	const PLUGIN_CLASS_NAME = ilSrReportPlugin::class;

	const CLASS_PLUGIN_ROUTER_GUI = 'ilUIPluginRouterGUI';
	const CLASS_PLUGIN_BASE_GUI = ilSrLpReportGUI::class;
	const CLASS_GUI = SingleObjectAllUserGUI::class;

	const CLASS_PATH_ARRAY = [self::CLASS_PLUGIN_ROUTER_GUI,self::CLASS_PLUGIN_BASE_GUI,self::CLASS_GUI];


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
	 * ReportListSingleObjectSingleUser constructor.
	 *
	 * @param int $obj_ref_id
	 */
	private function __construct(int $obj_ref_id) {
		$this->obj_ref_id = $obj_ref_id;

		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI,'ref_id',$this->getObjRefId());
		self::dic()->ctrl()->setParameterByClass(self::CLASS_GUI,'sr_rp',1);
	}


	/**
	 * @param int $obj_ref_id
	 *
	 * @return self
	 */
	public static function getInstance(int $obj_ref_id): self {
		if (!isset(self::$instances[$obj_ref_id])) {
			self::$instances[$obj_ref_id] = new self($obj_ref_id);
		}

		return self::$instances[$obj_ref_id];
	}

	/**
	 * @return string
	 */
	public function getLinkTarget():string {
		return self::dic()->ctrl()->getLinkTargetByClass(self::CLASS_PATH_ARRAY);
	}

	public function getGuiObject():SingleObjectAllUserGUI {
		return new SingleObjectAllUserGUI();
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

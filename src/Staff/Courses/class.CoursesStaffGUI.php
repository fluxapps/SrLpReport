<?php

namespace srag\Plugins\SrLpReport\Staff\Courses;

use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffGUI;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;

/**
 * Class CoursesStaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff\Courses
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\Courses\CoursesStaffGUI: srag\Plugins\SrLpReport\Staff\StaffGUI
 */
class CoursesStaffGUI extends AbstractStaffGUI {

	const TAB_ID = "courses";
	const CMD_SET_COURSE_FILTER = "setCourseFilter";


	/**
	 * @inheritdoc
	 */
	public function executeCommand()/*: void*/ {
		parent::executeCommand();

		$cmd = self::dic()->ctrl()->getCmd();

		switch ($cmd) {
			case self::CMD_SET_COURSE_FILTER:
				$this->{$cmd}();
				break;

			default:
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function setTabs()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractStaffTableGUI {
		$table = new CoursesTableGUI($this, $cmd);

		return $table;
	}


	/**
	 * @inheritdoc
	 */
	protected function getActionsArray(): array {
		return self::ilias()->staff()->courses()->getActionsArray();
	}


	/**
	 *
	 */
	protected function setCourseFilter()/*: void*/ {
		$crs_obj_id = intval(filter_input(INPUT_GET, Reports::GET_PARAM_COURSE_OBJ_ID));

		$table = $this->getTable(self::CMD_RESET_FILTER);
		$table->resetFilter();
		$table->resetOffset();

		$_POST["crs_title"] = self::dic()->objDataCache()->lookupTitle($crs_obj_id);
		$this->applyFilter();
	}
}

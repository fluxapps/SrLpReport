<?php

namespace srag\Plugins\SrLpReport\Staff\Courses;

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


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractStaffTableGUI {
		$table = new CoursesTableGUI($this, $cmd);

		return $table;
	}
}

<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class StaffGUI
 *
 * @package           srag\Plugins\SrLpReport\Staff
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Staff\StaffGUI: ilUIPluginRouterGUI
 */
class StaffGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const CMD_STAFF = "staff";
	const CMD_STAFF_APPLY_FILTER = "staffApplyFilter";
	const CMD_STAFF_RESET_FILTER = "staffResetFilter";
	const CMD_COURSES = "courses";
	const CMD_COURSES_APPLY_FILTER = "coursesApplyFilter";
	const CMD_COURSES_RESET_FILTER = "coursesResetFilter";
	const TAB_STAFF = "staff";
	const TAB_COURSES = "courses";


	/**
	 * StaffGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		$this->setTabs();

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				if (!self::access()->hasStaffAccess()) {
					ilUtil::sendFailure(self::dic()->language()->txt("permission_denied"), true);

					self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
				}

				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_STAFF:
					case self::CMD_STAFF_APPLY_FILTER:
					case self::CMD_STAFF_RESET_FILTER:
					case self::CMD_COURSES:
					case self::CMD_COURSES_APPLY_FILTER:
					case self::CMD_COURSES_RESET_FILTER:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs()/*: void*/ {
		self::dic()->language()->loadLanguageModule("mst");
		self::dic()->language()->loadLanguageModule("trac");

		self::dic()->tabs()->addTab(self::TAB_STAFF, self::dic()->language()->txt("my_staff"), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_STAFF));

		self::dic()->tabs()->addTab(self::TAB_COURSES, self::dic()->language()->txt("courses"), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_COURSES));
	}


	/**
	 * @param string $cmd
	 *
	 * @return StaffTableGUI
	 */
	protected function getStaffTable(string $cmd = self::CMD_STAFF): StaffTableGUI {
		$table = new StaffTableGUI($this, $cmd);

		return $table;
	}


	/**
	 *
	 */
	protected function staff()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_STAFF);

		$table = $this->getStaffTable();

		self::output()->output($table, true);
	}


	/**
	 *
	 */
	protected function staffApplyFilter()/*: void*/ {
		$table = $this->getStaffTable(self::CMD_STAFF_APPLY_FILTER);

		$table->writeFilterToSession();

		self::dic()->ctrl()->redirect($this, self::CMD_STAFF);
	}


	/**
	 *
	 */
	protected function staffResetFilter()/*: void*/ {
		$table = $this->getStaffTable(self::CMD_STAFF_RESET_FILTER);

		$table->resetFilter();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this, self::CMD_STAFF);
	}


	/**
	 * @param string $cmd
	 *
	 * @return CoursesTableGUI
	 */
	protected function getCoursesTable(string $cmd = self::CMD_COURSES): CoursesTableGUI {
		$table = new CoursesTableGUI($this, $cmd);

		return $table;
	}


	/**
	 *
	 */
	protected function courses()/*: void*/ {
		self::dic()->tabs()->activateTab(self::TAB_COURSES);

		$table = $this->getCoursesTable();

		self::output()->output($table, true);
	}


	/**
	 *
	 */
	protected function coursesApplyFilter()/*: void*/ {
		$table = $this->getCoursesTable(self::CMD_COURSES_APPLY_FILTER);

		$table->writeFilterToSession();

		self::dic()->ctrl()->redirect($this, self::CMD_COURSES);
	}


	/**
	 *
	 */
	protected function coursesResetFilter()/*: void*/ {
		$table = $this->getCoursesTable(self::CMD_COURSES_APPLY_FILTER);

		$table->resetFilter();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this, self::CMD_COURSES);
	}
}

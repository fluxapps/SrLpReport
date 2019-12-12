<?php

namespace srag\Plugins\SrLpReport\Staff;

use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilMStListCourse;
use ilMyStaffAccess;
use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Staff\CourseAdministration\CourseAdministrationStaffGUI;
use srag\Plugins\SrLpReport\Staff\Courses\CoursesStaffGUI;
use srag\Plugins\SrLpReport\Staff\User\UserStaffGUI;
use srag\Plugins\SrLpReport\Staff\Users\UsersStaffGUI;
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


	/**
	 * StaffGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		if (!self::access()->hasCurrentUserAccessToMyStaff()) {
			ilUtil::sendFailure(self::dic()->language()->txt("permission_denied"), true);
			self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
		}

		$this->setTabs();

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			case strtolower(UsersStaffGUI::class):
				self::dic()->ctrl()->forwardCommand(new UsersStaffGUI());
				break;
			case strtolower(UserStaffGUI::class):
				self::dic()->ctrl()->forwardCommand(new UserStaffGUI());
				break;
			case strtolower(CoursesStaffGUI::class):
				self::dic()->ctrl()->forwardCommand(new CoursesStaffGUI());
				break;
            case strtolower(CourseAdministrationStaffGUI::class):
                self::dic()->ctrl()->forwardCommand(new CourseAdministrationStaffGUI());
                break;
			default:
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs()/*: void*/ {
		self::dic()->language()->loadLanguageModule("mst");
		self::dic()->language()->loadLanguageModule("trac");

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("my_staff"));

        if (Config::getField(Config::KEY_ENABLE_USERS_VIEW)) {
            self::dic()->tabs()->addTab(UsersStaffGUI::TAB_ID, self::dic()->language()->txt("users"), self::dic()->ctrl()
                ->getLinkTargetByClass(UsersStaffGUI::class));
        }

        if (Config::getField(Config::KEY_ENABLE_COURSES_VIEW)) {
            self::dic()->tabs()->addTab(CoursesStaffGUI::TAB_ID, self::dic()->language()->txt("courses"), self::dic()->ctrl()
                ->getLinkTargetByClass(CoursesStaffGUI::class));
        }

        if (Config::getField(Config::KEY_ENABLE_COURSE_ADMINISTRATION)) {
            self::dic()->tabs()->addTab(CourseAdministrationStaffGUI::TAB_ID, self::plugin()->translate("title", CourseAdministrationStaffGUI::LANG_MODULE), self::dic()->ctrl()
                ->getLinkTargetByClass(CourseAdministrationStaffGUI::class));
        }
	}

	/**
	 * @param ilMStListCourse $my_staff_course
	 *
	 * @return string
	 */
	public static function getUserLpStatusAsHtml(ilMStListCourse $my_staff_course) {
		global $DIC;

		if (self::access()->hasCurrentUserAccessToLearningProgressInObject($my_staff_course->getCrsRefId())) {
			$lp_icon = $DIC->ui()->factory()->image()
				->standard(ilLearningProgressBaseGUI::_getImagePathForStatus($my_staff_course->getUsrLpStatus()), ilLearningProgressBaseGUI::_getStatusText(intval($my_staff_course->getUsrLpStatus())));

			$status = $DIC->ui()->renderer()->render($lp_icon) . ' '
				. ilLearningProgressBaseGUI::_getStatusText(intval($my_staff_course->getUsrLpStatus()));

			if($my_staff_course->getUsrLpStatus() == ilLPStatus::LP_STATUS_COMPLETED_NUM) {
				$status .= " - 100%";
			}

			return $status;
		}

		return '&nbsp';
	}

	/**
	 * @param ilMStListCourse $my_staff_course
	 *
	 * @return string
	 */
	public static function getUserLpStatusAsText(ilMStListCourse $my_staff_course) {
		if (self::access()->hasCurrentUserAccessToLearningProgressInObject
($my_staff_course->getCrsRefId())) {
			return ilLearningProgressBaseGUI::_getStatusText(intval($my_staff_course->getUsrLpStatus()));
		}

		return '';
	}
}

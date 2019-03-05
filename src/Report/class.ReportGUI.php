<?php

namespace srag\Plugins\SrLpReport\Report;

use ilLearningProgressGUI;
use ilLink;
use ilLPListOfSettingsGUI;
use ilObjCourseGUI;
use ilPanelGUI;
use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilTemplateException;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\Report\Matrix\MatrixReportGUI;
use srag\Plugins\SrLpReport\Report\Summary\SummaryReportGUI;
use srag\Plugins\SrLpReport\Report\User\UserReportGUI;
use srag\Plugins\SrLpReport\Staff\Courses\CoursesStaffGUI;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class BaseGUI
 *
 * @package           srag\Plugins\SrLpReport\Report
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\ReportGUI: ilUIPluginRouterGUI
 */
class ReportGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const TAB_LEARNING_PROGRESS = "learning_progress";
	const TAB_SETTINGS = "trac_settings";


	/**
	 * BaseGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {
		if (!self::access()->hasLPReadAccess(self::reports()->getReportObjRefId())) {
			ilUtil::sendFailure(self::dic()->language()->txt("permission_denied"), true);

			self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
		}

		$this->setTabs();

		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			case strtolower(UserReportGUI::class):
				self::dic()->ctrl()->forwardCommand(new UserReportGUI());
				break;
			case strtolower(MatrixReportGUI::class):
				self::dic()->ctrl()->forwardCommand(new MatrixReportGUI());
				break;
			case strtolower(SummaryReportGUI::class):
				self::dic()->ctrl()->forwardCommand(new SummaryReportGUI());
				break;
			default:
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs()/*: void*/ {
		self::dic()->language()->loadLanguageModule("trac");

		self::dic()->ctrl()->saveParameterByClass(ilLearningProgressGUI::class, Reports::GET_PARAM_REF_ID);
		self::dic()->ctrl()->saveParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID);

		self::dic()->ctrl()->saveParameterByClass(ReportGUI::class, "return");

		if (filter_input(INPUT_GET, "return")) {
			self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()->getLinkTargetByClass([
						ilUIPluginRouterGUI::class,
						StaffGUI::class,
						CoursesStaffGUI::class
					]));
		} else {
			self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("course"), ilLink::_getLink(self::reports()->getReportObjRefId()));
		}

		self::dic()->tabs()->addTab(self::TAB_LEARNING_PROGRESS, self::dic()->language()->txt("learning_progress"), self::dic()->ctrl()
			->getLinkTargetByClass([
					ilRepositoryGUI::class,
					ilObjCourseGUI::class,
					ilLearningProgressGUI::class
				]));
		self::dic()->tabs()->activateTab(self::TAB_LEARNING_PROGRESS);

		self::dic()->tabs()->addSubTabTarget(UserReportGUI::TAB_ID, self::dic()->ctrl()->getLinkTargetByClass([
				ilUIPluginRouterGUI::class,
				ReportGUI::class,
				UserReportGUI::class
			]));

		self::dic()->tabs()->addSubTabTarget(MatrixReportGUI::TAB_ID, self::dic()->ctrl()->getLinkTargetByClass([
				ilUIPluginRouterGUI::class,
				ReportGUI::class,
				MatrixReportGUI::class
			]));

		self::dic()->tabs()->addSubTabTarget(SummaryReportGUI::TAB_ID, self::dic()->ctrl()->getLinkTargetByClass([
				ilUIPluginRouterGUI::class,
				ReportGUI::class,
				SummaryReportGUI::class
			]));

		if (self::access()->hasLPWriteAccess(self::reports()->getReportObjRefId())) {
			self::dic()->tabs()->addSubTabTarget(self::TAB_SETTINGS, self::dic()->ctrl()->getLinkTargetByClass([
					ilRepositoryGUI::class,
					ilObjCourseGUI::class,
					ilLearningProgressGUI::class,
					ilLPListOfSettingsGUI::class
				]));
		}
	}


	/**
	 * @return string
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public static function getLegendHTML(): string {
		$tpl = self::plugin()->template("LearningProgress/legend.html", false, false);

		$tpl->setVariable("IMG_NOT_ATTEMPTED", ilUtil::getImagePath("scorm/not_attempted.svg"));
		$tpl->setVariable("IMG_IN_PROGRESS", ilUtil::getImagePath("scorm/incomplete.svg"));
		$tpl->setVariable("IMG_COMPLETED", ilUtil::getImagePath("scorm/completed.svg"));

		$tpl->setVariable("TXT_NOT_ATTEMPTED", self::dic()->language()->txt("trac_not_attempted"));
		$tpl->setVariable("TXT_IN_PROGRESS", self::dic()->language()->txt("trac_in_progress"));
		$tpl->setVariable("TXT_COMPLETED", self::dic()->language()->txt("trac_completed"));

		$panel = ilPanelGUI::getInstance();
		$panel->setPanelStyle(ilPanelGUI::PANEL_STYLE_PRIMARY);
		$panel->setBody($tpl->get());

		return self::output()->getHTML($panel->getHTML());
	}
}

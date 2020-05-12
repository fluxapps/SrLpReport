<?php

use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Block\CommentsCourseBlock53;
use srag\Plugins\SrLpReport\Block\CommentsCourseBlock54;
use srag\Plugins\SrLpReport\Block\CommentsPersonalDesktopBlock53;
use srag\Plugins\SrLpReport\Block\CommentsPersonalDesktopBlock54;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\Matrix\MatrixReportGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Report\Summary\SummaryReportGUI;
use srag\Plugins\SrLpReport\Report\User\UserReportGUI;
use srag\Plugins\SrLpReport\Staff\Courses\CoursesStaffGUI;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Staff\User\UserStaffGUI;
use srag\Plugins\SrLpReport\Staff\Users\UsersStaffGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class ilSrLpReportUIHookGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLpReportUIHookGUI extends ilUIHookPluginGUI
{

    use DICTrait;
    use SrLpReportTrait;
    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const PAR_TABS = "tabs";
    const PAR_SUB_TABS = "sub_tabs";
    const REDIRECT = "redirect";
    const TYPE_CRS = "crs";
    const TYPE_EXC = "exc";
    const TYPE_TST = "tst";
    const TYPES = [self::TYPE_CRS, self::TYPE_EXC, self::TYPE_TST];
    const PERSONAL_DESKTOP_INIT = "personal_desktop";
    const COURSES_INIT = "courses";
    const COMPONENT_PERSONAL_DESKTOP = "Services/PersonalDesktop";
    const COMPONENT_CONTAINER = "Services/Container";
    const PART_CENTER_RIGHT = "right_column";
    /**
     * @var bool[]
     */
    protected static $load
        = [
            self::REDIRECT => false
        ];


    /**
     * ilSrLpReportUIHookGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param string $a_comp
     * @param string $a_part
     * @param array  $a_par
     *
     * @return array
     */
    public function getHTML(/*string*/ $a_comp, /*string*/ $a_part, $a_par = []) : array
    {

        if (!self::$load[self::REDIRECT]) {

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLPListOfObjectsGUI::class)) {

                if (in_array(self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId())),self::TYPES)) {

                    self::$load[self::REDIRECT] = true;

                    switch (self::dic()->ctrl()->getCmd()) {
                        case "showUserObjectMatrix":
                        case "details":
                            $this->fixRedicrect();

                            self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID, self::reports()
                                ->getReportObjRefId());

                            self::dic()->ctrl()->redirectByClass([ilUIPluginRouterGUI::class, ReportGUI::class, MatrixReportGUI::class]);
                            break;

                        case "showObjectSummary":
                            $this->fixRedicrect();

                            self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID, self::reports()
                                ->getReportObjRefId());

                            self::dic()->ctrl()->redirectByClass([ilUIPluginRouterGUI::class, ReportGUI::class, SummaryReportGUI::class]);
                            break;

                        case "":
                            $this->fixRedicrect();

                            self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID, self::reports()
                                ->getReportObjRefId());

                            self::dic()->ctrl()->redirectByClass([ilUIPluginRouterGUI::class, ReportGUI::class, UserReportGUI::class]);
                            break;

                        default:
                            break;
                    }

                    return parent::getHTML($a_comp, $a_part, $a_par);
                }
            }

            if (Config::getField(Config::KEY_ENABLE_USERS_VIEW)) {
                if (self::dic()->ctrl()->getCmdClass() === strtolower(ilMyStaffGUI::class)
                    || self::dic()->ctrl()->getCmdClass() === strtolower(ilMStListUsersGUI::class)
                ) {

                    self::$load[self::REDIRECT] = true;

                    $this->fixRedicrect();

                    self::dic()->ctrl()->redirectByClass([
                        ilUIPluginRouterGUI::class,
                        StaffGUI::class,
                        UsersStaffGUI::class
                    ]);

                    return parent::getHTML($a_comp, $a_part, $a_par);
                }
            }

            if (Config::getField(Config::KEY_ENABLE_COURSES_VIEW)) {
                if (self::dic()->ctrl()->getCmdClass() === strtolower(ilMStListCoursesGUI::class)) {

                    self::$load[self::REDIRECT] = true;

                    $this->fixRedicrect();

                    self::dic()->ctrl()->redirectByClass([
                        ilUIPluginRouterGUI::class,
                        StaffGUI::class,
                        CoursesStaffGUI::class
                    ]);
                }
            }

            if (Config::getField(Config::KEY_ENABLE_USERS_VIEW)) {
                if (self::dic()->ctrl()->getCmdClass() === strtolower(ilMStShowUserGUI::class)) {

                    self::$load[self::REDIRECT] = true;

                    $this->fixRedicrect();

                    self::dic()->ctrl()->saveParameterByClass(StaffGUI::class, Reports::GET_PARAM_USR_ID);

                    self::dic()->ctrl()->redirectByClass([
                        ilUIPluginRouterGUI::class,
                        StaffGUI::class,
                        UserStaffGUI::class
                    ]);
                }
            }
        }

        if (Config::getField(Config::KEY_ENABLE_COMMENTS)) {

            if (!self::$load[self::PERSONAL_DESKTOP_INIT]) {

                if ($a_comp === self::COMPONENT_PERSONAL_DESKTOP && $a_part === self::PART_CENTER_RIGHT) {

                    self::$load[self::PERSONAL_DESKTOP_INIT] = true;

                    return [
                        "mode" => self::PREPEND,
                        "html" => self::output()->getHTML(self::version()
                            ->is54() ? new CommentsPersonalDesktopBlock54() : new CommentsPersonalDesktopBlock53())
                    ];
                }
            }

            if (!self::$load[self::COURSES_INIT]) {

                if (self::dic()->ctrl()->getCmdClass() === strtolower(ilObjCourseGUI::class) && $a_comp === self::COMPONENT_CONTAINER
                    && $a_part === self::PART_CENTER_RIGHT
                ) {

                    self::$load[self::COURSES_INIT] = true;

                    return [
                        "mode" => ilUIHookPluginGUI::PREPEND,
                        "html" => self::output()->getHTML(self::version()->is54() ? new CommentsCourseBlock54() : new CommentsCourseBlock53())
                    ];
                }
            }
        }

        return parent::getHTML($a_comp, $a_part, $a_par);
    }


    /**
     * @inheritDoc
     */
    public function modifyGUI(/*string*/ $a_comp, /*string*/ $a_part, /*array*/ $a_par = [])/*: void*/
    {
        if ($a_part === self::PAR_TABS) {
            if (in_array(self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId())),self::TYPES)) {
                foreach (self::dic()->tabs()->target as &$target) {
                    if ($target["id"] === "learning_progress") {
                        self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID, self::reports()->getReportObjRefId());
                        $target["link"] = self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, ReportGUI::class, MatrixReportGUI::class]);
                        break;
                    }
                }
            }
        }

        if ($a_part === self::PAR_SUB_TABS) {
            if (in_array(self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId(self::reports()->getReportObjRefId())),self::TYPES)) {
                if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLPListOfSettingsGUI::class)) {
                    self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID, self::reports()->getReportObjRefId());
                    self::dic()->tabs()->clearTargets();
                    ReportGUI::addTabs();
                    self::dic()->tabs()->activateSubTab(ReportGUI::TAB_SETTINGS);
                }
                if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLPListOfObjectsGUI::class) && self::dic()->ctrl()->getCmd() === "edituser") {
                    self::dic()->ctrl()->setParameterByClass(ReportGUI::class, Reports::GET_PARAM_REF_ID, self::reports()->getReportObjRefId());
                    self::dic()->tabs()->clearTargets();
                    ReportGUI::addTabs();
                }
            }
        }
    }


    /**
     *
     */
    protected function fixRedicrect()/*: void*/
    {
        self::dic()->ctrl()->setTargetScript("ilias.php"); // Fix ILIAS 5.3 bug
        self::dic()->ctrl()->initBaseClass(ilUIPluginRouterGUI::class); // Fix ILIAS bug
    }
}

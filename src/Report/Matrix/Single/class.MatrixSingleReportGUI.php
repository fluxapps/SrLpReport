<?php

namespace srag\Plugins\SrLpReport\Report\Matrix\Single;

use ilObjectFactory;
use ilObjectGUIFactory;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\Plugins\SrLpReport\Report\AbstractReportGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\Matrix\MatrixReportGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\StaffGUI;
use srag\Plugins\SrLpReport\Staff\User\UserStaffGUI;

/**
 * Class MatrixSingleReportGUI
 *
 * @package           srag\Plugins\SrLpReport\Report\Matrix\Single
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\Matrix\Single\MatrixSingleReportGUI: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class MatrixSingleReportGUI extends AbstractReportGUI
{

    const TAB_ID = "trac_matrix_single";
    const CMD_SET_PASSED = "setPassed";


    /**
     * @inheritdoc
     */
    public function executeCommand()/*: void*/
    {
        parent::executeCommand();

        $cmd = self::dic()->ctrl()->getCmd();

        switch ($cmd) {
            case self::CMD_SET_PASSED:
                $this->{$cmd}();
                break;

            default:
                break;
        }
    }


    /**
     * @inheritdoc
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->ctrl()->saveParameter($this, Reports::GET_PARAM_USR_ID);

        self::dic()->tabs()->clearTargets();

        if (!empty(filter_input(INPUT_GET, Reports::GET_PARAM_RETURN))) {
            self::dic()->ctrl()->saveParameterByClass(StaffGUI::class, Reports::GET_PARAM_USR_ID);

            self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                StaffGUI::class,
                UserStaffGUI::class
            ]));
        } else {
            self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                ReportGUI::class,
                MatrixReportGUI::class
            ]));
        }

        self::dic()->ui()->mainTemplate()->setHeaderActionMenu(self::output()->getHTML(self::reports()->getCellActions(self::reports()->getReportObjRefId(), self::reports()->getUsrId(), 0)));
    }


    /**
     * @inheritdoc
     */
    protected function getTable(string $cmd = self::CMD_INDEX) : AbstractReportTableGUI
    {
        return new MatrixSingleTableGUI($this, $cmd);
    }


    /**
     * @inheritdoc
     */
    protected function getActionsArray() : array
    {
        return [];
    }


    /**
     *
     */
    protected function setPassed()/*:void*/ {
        $object = ilObjectFactory::getInstanceByRefId(self::reports()->getReportObjRefId(), false);

        $passed = (!$object->getMembersObject()->hasPassed(self::reports()->getUsrId()));

        $object->getMembersObject()->updatePassed(self::reports()->getUsrId(), $passed, true);
        (new ilObjectGUIFactory())->getInstanceByRefId($object->getRefId())->updateLPFromStatus(self::reports()->getUsrId(), $passed);

        ilUtil::sendSuccess($passed ? self::plugin()->translate("set_passed") : self::plugin()->translate("set_in_progress"), true);

        self::dic()->ctrl()->redirectByClass([
            ilUIPluginRouterGUI::class,
            ReportGUI::class,
            MatrixReportGUI::class
        ]);
    }
}

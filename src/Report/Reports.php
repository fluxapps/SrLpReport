<?php

namespace srag\Plugins\SrLpReport\Report;

use ilExerciseHandlerGUI;
use ilExerciseManagementGUI;
use ILIAS\UI\Component\Dropdown\Dropdown;
use ilLearningProgressGUI;
use ilLPListOfObjectsGUI;
use ilLPObjSettings;
use ilLPStatus;
use ilObjectFactory;
use ilObjectGUIFactory;
use ilObjectLP;
use ilObjExerciseGUI;
use ilObjTest;
use ilObjTestGUI;
use ilOrgUnitPathStorage;
use ilOrgUnitPermissionQueries;
use ilOrgUnitPosition;
use ilOrgUnitUserAssignment;
use ilParticipantsTestResultsGUI;
use ilRepositoryGUI;
use ilSrLpReportPlugin;
use ilSrLpReportUIHookGUI;
use ilTestEvaluationGUI;
use ilTestParticipantList;
use ilTestResultsGUI;
use ilTestScoringGUI;
use ilTrQuery;
use ilUIPluginRouterGUI;
use srag\DIC\SrLpReport\DICTrait;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\ConfigPerObject\ConfigPerObjects;
use srag\Plugins\SrLpReport\Report\Matrix\Single\MatrixSingleReportGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use stdClass;

/**
 * Class Reports
 * @package srag\Plugins\SrLpReport\Report
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Reports
{

    use SrLpReportTrait;
    use DICTrait;

    const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_USR_ID = "usr_id";
    const GET_PARAM_ORG_UNIT_ID = "org_unit_id";
    const GET_PARAM_COURSE_OBJ_ID = "course_obj_id";
    const GET_PARAM_TARGET = "target";
    const GET_PARAM_RETURN = "return";
    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * Reports constructor
     */
    private function __construct()
    {

    }

    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return int|null
     */
    public function getReportObjRefId()/*: ?int*/
    {
        $obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

        if ($obj_ref_id === null) {
            $param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

            $obj_ref_id = explode("_", $param_target)[1];
        }

        $obj_ref_id = intval($obj_ref_id);

        if ($obj_ref_id > 0) {
            return $obj_ref_id;
        } else {
            return null;
        }
    }

    /**
     * @return int|null
     */
    public function getUsrId()/*: ?int*/
    {
        $usr_id = filter_input(INPUT_GET, self::GET_PARAM_USR_ID);

        $usr_id = intval($usr_id);

        if ($usr_id > 0) {
            return $usr_id;
        } else {
            return null;
        }
    }

    /**
     * @param int        $ref_id
     * @param array|null $user_ids
     * @return array
     */
    public function getChilds(int $ref_id,/*?*/ array $user_ids = null) : array
    {
        return array_unique(array_filter(array_merge(
            array_values(ilTrQuery::getObjectIds(self::dic()->objDataCache()->lookupObjId($ref_id), $ref_id, true,
                !empty($user_ids), $user_ids)["ref_ids"]),
            (!empty($always_show_types = Config::getField(Config::KEY_REPORTING_ALWAYS_SHOW_CHILD_TYPES)) ? self::dic()->database()->fetchAllCallback(self::dic()->database()->query(self::dic()->repositoryTree()->getSubTreeQuery($ref_id,
                [], $always_show_types)), function (stdClass $child) : int {
                return $child->child;
            }) : [])
        ), function (int $ref_id) : bool {
            return (ilObjectLP::getInstance(self::dic()->objDataCache()->lookupObjId($ref_id))->getCurrentMode() !== ilLPObjSettings::LP_MODE_DEACTIVATED);
        }));
    }

    /**
     * @param int $ref_id
     * @param int $user_id
     * @return Dropdown
     */
    public function getCellActions(
        int $ref_id,
        int $user_id,
        int $status
    ) : Dropdown {
        $actions = [];

        if (Config::getField(Config::KEY_SHOW_MATRIX_ACTIONS)) {
            switch (self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($ref_id))) {
                case ilSrLpReportUIHookGUI::TYPE_CRS:
                    $actions = array_merge(
                        $actions,
                        [
                        self::dic()->ui()->factory()->link()->standard((ilObjectFactory::getInstanceByRefId(
                            $ref_id,
                            false
                        )->getMembersObject()->hasPassed($user_id) ? self::plugin()
                                                                                  ->translate("set_in_progress") : self::plugin()->translate("set_passed")),
                            self::dic()->ctrl()->getLinkTargetByClass([
                                ilUIPluginRouterGUI::class,
                                ReportGUI::class,
                                MatrixSingleReportGUI::class
                            ], MatrixSingleReportGUI::CMD_SET_PASSED))
                    ]
                    );
                    break;

                case ilSrLpReportUIHookGUI::TYPE_TST:
                    self::dic()->ctrl()->setParameterByClass(ilParticipantsTestResultsGUI::class, "ref_id", $ref_id);

                    $actions = array_merge($actions, [
                        self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("all_results"),
                            self::dic()->ctrl()->getLinkTargetByClass([
                                ilRepositoryGUI::class,
                                ilObjTestGUI::class,
                                ilTestResultsGUI::class,
                                ilParticipantsTestResultsGUI::class
                            ]))->withOpenInNewViewport(true)
                    ]);

                    if (ilObjectFactory::getInstanceByRefId($ref_id, false)->getActiveIdOfUser($user_id) > 0) {
                        $obj_test = new ilObjTest($ref_id);
                        $il_test_participation_list = new ilTestParticipantList(
                            $obj_test
                        );

                        $il_test_participation_list->initializeFromDbRows(
                            $obj_test->getTestParticipantsForManualScoring()
                        );

                        if (
                            in_array(
                                $user_id, $il_test_participation_list->getAllUserIds()
                            ) === true) {
                            self::dic()->ctrl()->setParameterByClass(ilTestScoringGUI::class, "ref_id", $ref_id);
                            self::dic()->ctrl()->setParameterByClass(ilTestScoringGUI::class, "active_id",
                                ilObjectFactory::getInstanceByRefId($ref_id, false)->getActiveIdOfUser($user_id));

                            $actions = array_merge($actions, [
                                self::dic()->ui()->factory()->link()->standard(
                                    self::plugin()->translate("per_participant"),
                                    self::dic()->ctrl()->getLinkTargetByClass([
                                        ilRepositoryGUI::class,
                                        ilObjTestGUI::class,
                                        ilTestScoringGUI::class
                                    ], "showManScoringParticipantScreen"))->withOpenInNewViewport(true)
                            ]);
                        }

                    }

                    break;

                case ilSrLpReportUIHookGUI::TYPE_EXC:
                    self::dic()->ctrl()->setParameterByClass(ilExerciseManagementGUI::class, "ref_id", $ref_id);
                    self::dic()->ctrl()->setParameterByClass(ilExerciseManagementGUI::class, "part_id", $user_id);
                    $actions = array_merge($actions, [
                        self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("all_results"),
                            self::dic()->ctrl()->getLinkTargetByClass([
                                ilExerciseHandlerGUI::class,
                                ilObjExerciseGUI::class,
                                ilExerciseManagementGUI::class
                            ], "members"))->withOpenInNewViewport(true)
                    ]);

                    self::dic()->ctrl()->setParameterByClass(ilExerciseManagementGUI::class, "ref_id", $ref_id);
                    $actions = array_merge($actions, [
                        self::dic()->ui()->factory()->link()->standard(self::dic()->language()->txt("exc_participant_view",
                            "exc"), self::dic()->ctrl()->getLinkTargetByClass([
                            ilExerciseHandlerGUI::class,
                            ilObjExerciseGUI::class,
                            ilExerciseManagementGUI::class
                        ], "showParticipant"))->withOpenInNewViewport(true)
                    ]);
                    break;

                default:
                    break;
            }
        }

        if (!empty($actions)) {
            if (Config::getField(Config::KEY_SHOW_MATRIX_ACTIONS_EDIT)) {

                        self::dic()->ctrl()->setParameterByClass(ilLPListOfObjectsGUI::class, "ref_id", $ref_id);
                        self::dic()->ctrl()->setParameterByClass(ilLPListOfObjectsGUI::class, "details_id", $ref_id);
                        self::dic()->ctrl()->setParameterByClass(ilLPListOfObjectsGUI::class, "user_id", $user_id);
                        $actions = array_merge($actions, [
                            self::dic()->ui()->factory()->link()->standard(self::dic()->language()->txt("edit"),
                                self::dic()->ctrl()->getLinkTargetByClass([
                                    ilRepositoryGUI::class,
                                    get_class((new ilObjectGUIFactory())->getInstanceByRefId($ref_id)),
                                    ilLearningProgressGUI::class,
                                    ilLPListOfObjectsGUI::class
                                ], "edituser"))
                        ]);

                }


            }


        return self::dic()->ui()->factory()->dropdown()->standard($actions)->withLabel(self::dic()->language()->txt("actions"));
    }




/**
 * @param int        $user_id
 * @param array|null $ref_ids
 * @return array
 */
public
function getAssignedOrgUnits(int $user_id, /*?*/ array $ref_ids = null) : array
{
    $where = ilOrgUnitUserAssignment::where([
        "user_id" => $user_id
    ]);

    if (!empty($ref_ids)) {
        $where = $where->where([
            "orgu_id" => $ref_ids
        ], "IN");
    }

    $ref_ids = $where->getArray(null, "orgu_id");

    if (empty($ref_ids)) {
        return [];
    }

    return ilOrgUnitPathStorage::orderBy("path")->where([
        "ref_id" => $ref_ids
    ], "IN")->getArray("ref_id", "path");
}

/**
 * @return ConfigPerObjects
 */
public
function configPerObjects() : ConfigPerObjects
{
    return ConfigPerObjects::getInstance();
}

/**
 * @param int $parent_ref_id
 * @return bool
 */
public
function shouldSyncPositionPermissionsWithChildren(int $parent_ref_id) : bool
{
    if (!Config::getField(Config::KEY_SYNC_POSITION_PERMISSIONS_WITH_CHILDREN)) {
        return false;
    }

    if (!in_array(self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($parent_ref_id)),
        ilSrLpReportUIHookGUI::TYPES)) {
        return false;
    }

    return true;
}

/**
 * @param int $parent_ref_id
 * @param int $child_ref_id
 */
public
function syncPositionPermissionsWithChildren(int $parent_ref_id, int $child_ref_id)/*:void*/
{
    if (!$this->shouldSyncPositionPermissionsWithChildren($parent_ref_id)) {
        return;
    }

    foreach (ilOrgUnitPosition::get() as $position) {
        $parent_permissions = ilOrgUnitPermissionQueries::getSetForRefId($parent_ref_id, $position->getId());

        if ($parent_permissions->isProtected()) {
            continue;
        }

        $child_permissions = ilOrgUnitPermissionQueries::getSetForRefId($child_ref_id, $position->getId());

        $child_permissions->setProtected($parent_permissions->isProtected());

        $child_permissions->setOperations($parent_permissions->getOperations());

        $child_permissions->store();
    }
}

/**
 * @param int $parent_ref_id
 */
public
function syncPositionPermissionsWithChildrens(int $parent_ref_id)/*:void*/
{
    if (!$this->shouldSyncPositionPermissionsWithChildren($parent_ref_id)) {
        return;
    }

    foreach ($this->getChilds($parent_ref_id) as $child_ref_id) {
        $this->syncPositionPermissionsWithChildren($parent_ref_id, $child_ref_id);
    }
}
}

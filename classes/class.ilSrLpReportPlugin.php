<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\CommentsUI\SrLpReport\Utils\CommentsUITrait;
use srag\LibraryLanguageInstaller\SrLpReport\LibraryLanguageInstaller;
use srag\Plugins\SrLpReport\Comment\Ctrl\AbstractCtrl;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Report\ConfigPerObject\ConfigPerObject;
use srag\Plugins\SrLpReport\Staff\CourseAdministration\CourseAdministrationEnrollment;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use srag\RemovePluginDataConfirm\SrLpReport\PluginUninstallTrait;

/**
 * Class ilSrLpReportPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLpReportPlugin extends ilUserInterfaceHookPlugin
{

    use PluginUninstallTrait;
    use SrLpReportTrait;
    use CommentsUITrait;
    const PLUGIN_ID = "srlprep";
    const PLUGIN_NAME = "SrLpReport";
    const PLUGIN_CLASS_NAME = self::class;
    const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = SrLpReportRemoveDataConfirm::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


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
     * ilSrLpReportPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    protected function init()/*:void*/
    {
        AbstractCtrl::init();
    }


    /**
     * @return string
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function handleEvent(/*string*/ $a_component, /*string*/ $a_event,/*array*/ $a_parameter)/*: void*/
    {
        switch ($a_component) {
            case "Modules/Course":
                switch ($a_event) {
                    case "addParticipant":
                        self::ilias()->staff()->courseAdministration()->setEnrolmentTime($a_parameter["obj_id"], $a_parameter["usr_id"], time());
                        break;

                    case "deleteParticipant":
                        self::ilias()->staff()->courseAdministration()->setSignoutDate($a_parameter["obj_id"], $a_parameter["usr_id"], time());
                        break;

                    default:
                        break;
                }
                break;

            case "Services/Object":
                switch ($a_event) {
                    case "putObjectInTree":
                        self::reports()->syncPositionPermissionsWithChildren(intval($a_parameter["parent_ref_id"]), intval($a_parameter["object"]->getRefId()));
                        break;

                    case "update":
                        self::reports()->syncPositionPermissionsWithChildrens(intval($a_parameter["ref_id"]));
                        break;

                    default:
                        break;
                }
                break;

            default:
                break;
        }
    }


    /**
     * @inheritdoc
     */
    public function updateLanguages($a_lang_keys = null)
    {
        parent::updateLanguages($a_lang_keys);

        LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
            . "/../vendor/srag/removeplugindataconfirm/lang")->updateLanguages();

        self::comments()->installLanguages();

        LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
            . "/../vendor/srag/custominputguis/src/TableGUI/lang")->updateLanguages();
    }


    /**
     * @inheritdoc
     */
    protected function deleteData()/*: void*/
    {
        self::dic()->database()->dropTable(Config::TABLE_NAME, false);
        self::comments()->dropTables();
        self::dic()->database()->dropTable(CourseAdministrationEnrollment::TABLE_NAME, false);
        self::dic()->database()->dropAutoIncrementTable(CourseAdministrationEnrollment::TABLE_NAME);
        self::dic()->database()->dropTable(ConfigPerObject::TABLE_NAME, false);
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return false;
    }
}

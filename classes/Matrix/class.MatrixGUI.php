<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use srag\DIC\SrLpReport\DICTrait;

/**
 * Class MatrixGUI
 *
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy MatrixGUI: ilUIPluginRouterGUI
 */
class MatrixGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const TAB_ID = "srcrslpmatrix";
	const CMD_EDIT = "edit";
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_INDEX = 'index';
	const CMD_RESET_FILTER = 'resetFilter';
	const CMD_MAIL_SELECTED_USERS = 'mailselectedusers';
	const TABLE_GUI_CLASS_NAME = "MatrixTableGUI";
	/**
	 * @var \SingleObjectAllUserTableGUI
	 */
	protected $table;


	/**
	 * MatrixGUI constructor
	 */
	public function __construct() {
		self::tabgui()->setTabs();

		$type = self::dic()->objDataCache()->lookupType(ilObject::_lookupObjectId($_GET['ref_id']));
		$icon = ilObject::_getIcon("", "tiny", $type);

		self::dic()->mainTemplate()->setTitleIcon($icon);

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("learning_progress") . " "
			. ilObject::_lookupTitle(ilObject::_lookupObjectId($_GET['ref_id'])));
	}


	/**
	 *
	 */
	public function executeCommand()/*: void*/ {

		self::dic()->ctrl()->saveParameter($this, 'ref_id');
		self::dic()->ctrl()->saveParameter($this, 'details_id');

		$cmd = self::dic()->ctrl()->getCmd();
		switch ($cmd) {
			case self::CMD_RESET_FILTER:
			case self::CMD_APPLY_FILTER:
			case self::CMD_INDEX:
			case self::CMD_MAIL_SELECTED_USERS:
				$this->$cmd();
				break;
			default:
				$this->index();
				break;
		}
	}


	public function mailselectedusers() {
		// see ilObjCourseGUI::sendMailToSelectedUsersObject()

		if (count($_POST["usr_id"]) == 0) {
			ilUtil::sendFailure(self::dic()->language()->txt("no_checkbox"), true);
			self::dic()->ctrl()->redirect($this);
		}

		$rcps = array();
		foreach ($_POST["usr_id"] as $usr_id) {
			$rcps[] = ilObjUser::_lookupLogin($usr_id);
		}

		$template = array();
		$sig = NULL;

		// repository-object-specific
		$ref_id = (int)$_REQUEST["ref_id"];
		if ($ref_id) {
			$obj_lp = ilObjectLP::getInstance(ilObject::_lookupObjectId($ref_id));
			$tmpl_id = $obj_lp->getMailTemplateId();

			if ($tmpl_id) {
				$template = array(
					ilMailFormCall::CONTEXT_KEY => $tmpl_id,
					'ref_id' => $ref_id,
					'ts' => time()
				);
			} else {
				include_once './Services/Link/classes/class.ilLink.php';
				$sig = ilLink::_getLink($ref_id);
				$sig = rawurlencode(base64_encode($sig));
			}
		}

		ilUtil::redirect(ilMailFormCall::getRedirectTarget($this, self::dic()->ctrl()->getCmd(), array(), array(
				'type' => 'new',
				'rcp_to' => implode(',', $rcps),
				'sig' => $sig
			), $template));
	}


	public function index() {
		$this->listUsers();
	}


	public function listUsers() {
		$table_class_name = self::TABLE_GUI_CLASS_NAME;

		$this->table = new $table_class_name($this, self::dic()->ctrl()->getCmd());
		self::output()->output($this->getTableAndFooterHtml(), true);
	}


	public function applyFilter() {
		$table_class_name = self::TABLE_GUI_CLASS_NAME;

		$this->table = new $table_class_name($this, self::dic()->ctrl()->getCmd());
		$this->table->writeFilterToSession();
		$this->table->resetOffset();
		self::dic()->ctrl()->redirect($this);
	}


	public function resetFilter() {
		$table_class_name = self::TABLE_GUI_CLASS_NAME;

		$this->table = new $table_class_name($this, self::dic()->ctrl()->getCmd());
		$this->table->resetOffset();
		$this->table->resetFilter();
		self::dic()->ctrl()->redirect($this);
	}


	public function getTableAndFooterHtml() {

		self::dic()->language()->loadLanguageModule('trac');

		$tpl = self::plugin()->template("Report/report.html", false, false);
		$tpl->setVariable("REPORT", $this->table->getHTML());
		$tpl->setVariable('LEGEND', ilSrLpReportGUI::getLegendHTML());

		return self::output()->getHTML($tpl);
	}
}

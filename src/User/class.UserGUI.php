<?php

namespace srag\Plugins\SrLpReport\User;

use ilLink;
use ilMailFormCall;
use ilObject;
use ilObjectLP;
use ilObjUser;
use ilSrLpReportPlugin;
use ilTemplateException;
use ilUtil;
use srag\DIC\SrLpReport\DICTrait;
use srag\DIC\SrLpReport\Exception\DICException;
use srag\Plugins\SrLpReport\GUI\BaseGUI;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class UserGUI
 *
 * @package           srag\Plugins\SrLpReport\User
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\User\UserGUI: srag\Plugins\SrLpReport\GUI\BaseGUI
 */
class UserGUI {

	use DICTrait;
	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const CMD_EDIT = "edit";
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_INDEX = 'index';
	const CMD_RESET_FILTER = 'resetFilter';
	const CMD_MAIL_SELECTED_USERS = 'mailselectedusers';
	const TAB_ID = "srcrslpuser";
	/**
	 * @var UserTableGUI
	 */
	protected $table;


	/**
	 * UserGUI constructor
	 */
	public function __construct() {
		self::tabgui()->setTabs();

		$type = self::dic()->objDataCache()->lookupType(ilObject::_lookupObjectId($_GET['ref_id']));
		$icon = ilObject::_getIcon("", "tiny", $type);

		self::dic()->mainTemplate()->setTitleIcon($icon);

		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("learning_progress") . " "
			. ilObject::_lookupTitle(ilObject::_lookupObjectId($_GET['ref_id'])));

		self::dic()->mainTemplate()->setVariable('LEGEND', "222");
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


	/**
	 *
	 */
	public function mailselectedusers() {
		// see ilObjCourseGUI::sendMailToSelectedUsersObject()

		if (count($_POST["usr_id"]) == 0) {
			ilUtil::sendFailure(self::dic()->language()->txt("no_checkbox"), false);
			self::dic()->ctrl()->redirect($this);
		}

		$rcps = [];
		foreach ($_POST["usr_id"] as $usr_id) {
			$rcps[] = ilObjUser::_lookupLogin($usr_id);
		}

		$template = [];
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
				$sig = ilLink::_getLink($ref_id);
				$sig = rawurlencode(base64_encode($sig));
			}
		}

		ilUtil::redirect(ilMailFormCall::getRedirectTarget($this, self::dic()->ctrl()->getCmd(), [], array(
			'type' => 'new',
			'rcp_to' => implode(',', $rcps),
			'sig' => $sig
		), $template));
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function index() {
		$this->listUsers();
	}


	/**
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function listUsers() {
		$this->table = new UserTableGUI($this, self::dic()->ctrl()->getCmd());

		self::output()->output($this->getTableAndFooterHtml(), true);
	}


	/**
	 *
	 */
	public function applyFilter() {
		$this->table = new UserTableGUI($this, self::dic()->ctrl()->getCmd());
		$this->table->writeFilterToSession();
		$this->table->resetOffset();
		self::dic()->ctrl()->redirect($this);
	}


	/**
	 *
	 */
	public function resetFilter() {
		$this->table = new UserTableGUI($this, self::dic()->ctrl()->getCmd());
		$this->table->resetOffset();
		$this->table->resetFilter();
		self::dic()->ctrl()->redirect($this);
	}


	/**
	 * @return string
	 * @throws DICException
	 * @throws ilTemplateException
	 */
	public function getTableAndFooterHtml(): string {
		self::dic()->language()->loadLanguageModule('trac');

		$tpl = self::plugin()->template("Report/report.html", false, false);
		$tpl->setVariable("REPORT", self::output()->getHTML($this->table));
		$tpl->setVariable('LEGEND', BaseGUI::getLegendHTML());

		return self::output()->getHTML($tpl);
	}
}

<?php

namespace srag\Plugins\SrLpReport\Matrix;

use ilLink;
use ilMailFormCall;
use ilObject;
use ilObjectLP;
use ilObjUser;
use ilUtil;
use srag\CustomInputGUIs\SrLpReport\TableGUI\TableGUI;
use srag\Plugins\SrLpReport\GUI\AbstractGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;

/**
 * Class MatrixGUI
 *
 * @package           srag\Plugins\SrLpReport\Matrix
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Matrix\MatrixGUI: srag\Plugins\SrLpReport\GUI\BaseGUI
 */
class MatrixGUI extends AbstractGUI {

	const TAB_ID = "trac_matrix";
	const CMD_MAIL_SELECTED_USERS = 'mailselectedusers';


	/**
	 * @inheritdoc
	 */
	public function executeCommand()/*: void*/ {
		parent::executeCommand();

		$cmd = self::dic()->ctrl()->getCmd();

		switch ($cmd) {
			case self::CMD_MAIL_SELECTED_USERS:
				$this->{$cmd}();
				break;

			default:
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(): TableGUI {
		return new MatrixTableGUI($this, self::dic()->ctrl()->getCmd());
	}


	/**
	 *
	 */
	protected function mailselectedusers()/*: void*/ {
		// see ilObjCourseGUI::sendMailToSelectedUsersObject()

		if (count($_POST["usr_id"]) == 0) {
			ilUtil::sendFailure(self::dic()->language()->txt("no_checkbox"), true);
			self::dic()->ctrl()->redirect($this);
		}

		$rcps = [];
		foreach ($_POST["usr_id"] as $usr_id) {
			$rcps[] = ilObjUser::_lookupLogin($usr_id);
		}

		$template = [];
		$sig = NULL;

		// repository-object-specific
		$ref_id = ReportFactory::getReportObjRefId();
		if ($ref_id) {
			$obj_lp = ilObjectLP::getInstance(ilObject::_lookupObjectId($ref_id));
			$tmpl_id = $obj_lp->getMailTemplateId();

			if ($tmpl_id) {
				$template = array(
					ilMailFormCall::CONTEXT_KEY => $tmpl_id,
					"ref_id" => $ref_id,
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
}

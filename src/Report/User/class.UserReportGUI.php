<?php

namespace srag\Plugins\SrLpReport\Report\User;

use ilLink;
use ilMailFormCall;
use ilObjectLP;
use ilObjUser;
use ilUtil;
use srag\Plugins\SrLpReport\Report\AbstractReportGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;

/**
 * Class UserReportGUI
 *
 * @package           srag\Plugins\SrLpReport\Report\User
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\User\UserReportGUI: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class UserReportGUI extends AbstractReportGUI {

	const TAB_ID = "trac_crs_objects";
	const CMD_EDIT = "edit";
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
	protected function getTable(): AbstractReportTableGUI {
		return new UserTableGUI($this, self::dic()->ctrl()->getCmd());
	}


	/**
	 *
	 */
	protected function mailselectedusers()/*: void*/ {
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
		$ref_id = ReportFactory::getReportObjRefId();
		if ($ref_id) {
			$obj_lp = ilObjectLP::getInstance(self::dic()->objDataCache()->lookupObjId($ref_id));
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

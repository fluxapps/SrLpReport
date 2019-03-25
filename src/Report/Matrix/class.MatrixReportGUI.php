<?php

namespace srag\Plugins\SrLpReport\Report\Matrix;

use ilLink;
use ilMailFormCall;
use ilObjectLP;
use ilObjUser;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\Plugins\SrLpReport\Report\AbstractReportGUI;
use srag\Plugins\SrLpReport\Report\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\Matrix\Single\MatrixSingleReportGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;
use srag\Plugins\SrLpReport\Report\Reports;

/**
 * Class MatrixReportGUI
 *
 * @package           srag\Plugins\SrLpReport\Report\Matrix
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Report\Matrix\MatrixReportGUI: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class MatrixReportGUI extends AbstractReportGUI {

	const TAB_ID = "trac_matrix";
	const CMD_MAIL_SELECTED_USERS = 'mailselectedusers';
	const CMD_GET_ACTIONS = "getActions";


	/**
	 * @inheritdoc
	 */
	public function executeCommand()/*: void*/ {
		parent::executeCommand();

		$cmd = self::dic()->ctrl()->getCmd();

		switch ($cmd) {
			case self::CMD_MAIL_SELECTED_USERS:
			case self::CMD_GET_ACTIONS:
				$this->{$cmd}();
				break;

			default:
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function setTabs()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function getTable(string $cmd = self::CMD_INDEX): AbstractReportTableGUI {
		return new MatrixTableGUI($this, $cmd);
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
		$sig = null;

		// repository-object-specific
		$ref_id = self::reports()->getReportObjRefId();
		if ($ref_id) {
			$obj_lp = ilObjectLP::getInstance(self::dic()->objDataCache()->lookupObjId($ref_id));
			$tmpl_id = $obj_lp->getMailTemplateId();

			if ($tmpl_id) {
				$template = [
					ilMailFormCall::CONTEXT_KEY => $tmpl_id,
					"ref_id" => $ref_id,
					'ts' => time()
				];
			} else {
				$sig = ilLink::_getLink($ref_id);
				$sig = rawurlencode(base64_encode($sig));
			}
		}

		ilUtil::redirect(ilMailFormCall::getRedirectTarget($this, self::dic()->ctrl()->getCmd(), [], [
			'type' => 'new',
			'rcp_to' => implode(',', $rcps),
			'sig' => $sig
		], $template));
	}


	/**
	 *
	 */
	protected function getActions()/*: void*/ {
		self::dic()->ctrl()->saveParameterByClass(ReportGUI::class, Reports::GET_PARAM_USR_ID);

		self::output()->output([
			self::dic()->ui()->factory()->button()->shy(self::dic()->language()->txt("details"), self::dic()->ctrl()->getLinkTargetByClass([
				ilUIPluginRouterGUI::class,
				ReportGUI::class,
				MatrixSingleReportGUI::class
			]))
		]);
	}
}

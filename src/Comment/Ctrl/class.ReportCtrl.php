<?php

namespace srag\Plugins\SrLpReport\Comment\Ctrl;

use ilUIPluginRouterGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;

/**
 * Class ReportCtrl
 *
 * @package           srag\Plugins\SrLpReport\Comment\Ctrl
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Comment\Ctrl\ReportCtrl: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class ReportCtrl extends AbstractCtrl {

	/**
	 * @inheritdoc
	 */
	public function getAsyncClass(): array {
		self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REPORT_OBJ_ID, self::dic()->objDataCache()->lookupObjId(self::reports()
			->getReportObjRefId()));

		self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REPORT_USER_ID, self::reports()->getUsrId());

		return [
			ilUIPluginRouterGUI::class,
			ReportGUI::class,
			self::class
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getCommentsArray(int $report_obj_id, int $report_user_id): array {
		return self::comments(self::COMMENTS_CLASS_NAME)->getCommentsForReport($report_obj_id, $report_user_id);
	}
}

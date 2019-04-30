<?php

namespace srag\Plugins\SrLpReport\Comment\Ctrl;

use ilUIPluginRouterGUI;

/**
 * Class CourseCtrl
 *
 * @package           srag\Plugins\SrLpReport\Comment\Ctrl
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Comment\Ctrl\CourseCtrl: ilUIPluginRouterGUI
 */
class CourseCtrl extends AbstractCtrl {

	/**
	 * @inheritdoc
	 */
	public function getAsyncClass(): array {
		self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REPORT_OBJ_ID, self::dic()->objDataCache()->lookupObjId(sel::reports()->getReportObjRefId()));

		self::dic()->ctrl()->setParameter($this, self::GET_PARAM_REPORT_USER_ID, self::dic()->user()->getId());

		return [
			ilUIPluginRouterGUI::class,
			self::class
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getCommentsArray(int $report_obj_id, int $report_user_id): array {
		return self::comments()->getCommentsForCurrentUser($report_obj_id);
	}


	/**
	 * @inheritdoc
	 */
	public function getIsReadOnly(): bool {
		return true;
	}


	/**
	 * @inheritdoc
	 */
	protected function createComment()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function updateComment()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function deleteComment()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function shareComment()/*: void*/ {

	}
}

<?php

namespace srag\Plugins\SrLpReport\Comment\Ctrl;

use ilUIPluginRouterGUI;

/**
 * Class PersonalDesktopCtrl
 *
 * @package           srag\Plugins\SrLpReport\Comment\Ctrl
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Comment\Ctrl\PersonalDesktopCtrl: ilUIPluginRouterGUI
 */
class PersonalDesktopCtrl extends AbstractCtrl {

	/**
	 * @inheritdoc
	 */
	public function getAsyncClass(): array {
		return [
			ilUIPluginRouterGUI::class,
			self::class
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getCommentsArray(int $report_obj_id, int $report_user_id): array {
		return self::comments()->withOutputObjectTitles(true)->getCommentsForCurrentUser();
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
	protected function createComment(): void {

	}


	/**
	 * @inheritdoc
	 */
	protected function updateComment(): void {

	}


	/**
	 * @inheritdoc
	 */
	protected function deleteComment(): void {

	}


	/**
	 * @inheritdoc
	 */
	protected function shareComment(): void {

	}
}

<?php

namespace srag\Plugins\SrLpReport\Block;

use ilSrLpReportPlugin;

/**
 * Class CommentsCourseBlock54
 *
 * @package srag\Plugins\SrLpReport\Block
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CommentsCourseBlock54 extends BaseCommentsCourseBlock {

	/**
	 * @return string
	 */
	public function getBlockType(): string {
		return ilSrLpReportPlugin::PLUGIN_ID;
	}


	/**
	 * @return bool
	 */
	protected function isRepositoryObject(): bool {
		return false;
	}
}

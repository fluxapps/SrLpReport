<?php

namespace srag\Plugins\SrLpReport\Block;

use ilSrLpReportPlugin;

/**
 * Class CommentsCourseBlock53
 *
 * @package srag\Plugins\SrLpReport\Block
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CommentsCourseBlock53 extends BaseCommentsCourseBlock {

	/**
	 * @return string
	 */
	public static function getBlockType(): string {
		return ilSrLpReportPlugin::PLUGIN_ID;
	}


	/**
	 * @return bool
	 */
	public static function isRepositoryObject(): bool {
		return false;
	}
}

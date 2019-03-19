<?php

namespace srag\Plugins\SrLpReport\Block;

use ilSrLpReportPlugin;

/**
 * Class CommentsBlock53
 *
 * @package srag\Plugins\SrLpReport\Block
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CommentsBlock53 extends BaseCommentsBlock {

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

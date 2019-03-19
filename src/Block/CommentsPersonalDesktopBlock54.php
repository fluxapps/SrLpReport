<?php

namespace srag\Plugins\SrLpReport\Block;

use ilSrLpReportPlugin;

/**
 * Class CommentsPersonalDesktopBlock54
 *
 * @package srag\Plugins\SrLpReport\Block
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CommentsPersonalDesktopBlock54 extends BaseCommentsPersonalDesktopBlock {

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

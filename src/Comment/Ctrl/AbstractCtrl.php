<?php

namespace srag\Plugins\SrLpReport\Comment\Ctrl;

use ilSrLpReportPlugin;
use srag\CommentsUI\SrLpReport\Ctrl\AbstractCtrl as AbstractCtrl_;
use srag\Plugins\SrLpReport\Comment\Comment;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class AbstractCtrl
 *
 * @package srag\Plugins\SrLpReport\Comment\Ctrl
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractCtrl extends AbstractCtrl_ {

	use SrLpReportTrait;
	const PLUGIN_CLASS_NAME = ilSrLpReportPlugin::class;
	const COMMENTS_CLASS_NAME = Comment::class;


	/**
	 * @inheritdoc
	 */
	public function executeCommand()/*: void*/ {
		if (Config::getField(Config::KEY_ENABLE_COMMENTS)) {
			parent::executeCommand();
		}
	}
}

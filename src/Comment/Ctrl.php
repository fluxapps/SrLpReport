<?php

namespace srag\Plugins\SrLpReport\Comment;

use srag\CommentsUI\SrLpReport\UI\Ctrl as CommentCtrl;

/**
 * Class Ctrl
 *
 * @package           srag\Plugins\SrLpReport\Comment
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLpReport\Comment\Ctrl: srag\Plugins\SrLpReport\Report\ReportGUI
 */
class Ctrl extends CommentCtrl {

	const COMMENTS_CLASS_NAME = Comment::class;
}

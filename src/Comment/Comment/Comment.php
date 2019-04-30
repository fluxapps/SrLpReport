<?php

namespace srag\Plugins\SrLpReport\Comment\Comment;

use srag\CommentsUI\SrLpReport\Comment\AbstractComment;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;

/**
 * Class Comment
 *
 * @package srag\Plugins\SrLpReport\Comment\Comment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Comment extends AbstractComment {

	use SrLpReportTrait;
	const TABLE_NAME = "ui_uihk_srlprep_com";
}

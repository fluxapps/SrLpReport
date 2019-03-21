<?php

namespace srag\CommentsUI\SrLpReport\Utils;

use srag\CommentsUI\SrLpReport\Comment\Repository as CommentsRepository;
use srag\CommentsUI\SrLpReport\UI\UI as CommentsUI;

/**
 * Trait CommentsUITrait
 *
 * @package srag\CommentsUI\SrLpReport\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait CommentsUITrait {

	/**
	 * @return CommentsRepository
	 */
	protected static function comments(): CommentsRepository {
		return CommentsRepository::getInstance();
	}


	/**
	 * @return CommentsUI
	 */
	protected static function commentsUI(): CommentsUI {
		return new CommentsUI();
	}
}

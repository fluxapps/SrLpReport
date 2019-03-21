<?php

namespace srag\CommentsUI\SrLpReport\UI;

use ilTemplate;
use srag\CommentsUI\SrLpReport\Comment\Comment;
use srag\DIC\SrLpReport\DICTrait;

/**
 * Class UI
 *
 * @package srag\CommentsUI\SrLpReport\UI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UI {

	use DICTrait;
	/**
	 * @var bool
	 */
	protected static $init = false;
	/**
	 * @var Comment[]
	 */
	protected $comments = [];


	/**
	 * UI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param Comment[] $comments
	 *
	 * @return self
	 */
	public function withComments(array $comments): self {
		$this->comments = $comments;

		return $this;
	}


	/**
	 *
	 */
	private function initJs()/*: void*/ {
		if (self::$init === false) {
			self::$init = true;

			$dir = __DIR__;
			$dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

			self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/jquery-comments/js/jquery-comments.js");
			self::dic()->mainTemplate()->addCss($dir . "/../../node_modules/jquery-comments/css/jquery-comments.css");

			self::dic()->mainTemplate()->addJavaScript($dir . "/../../js/commentsui.min.js");
			self::dic()->mainTemplate()->addCss($dir . "/../../css/commentsui.css");
		}
	}


	/**
	 * @return string
	 */
	public function render(): string {
		$this->initJs();

		$tpl = new ilTemplate(__DIR__ . "/../../templates/commentsui.html", false, false);

		return self::output()->getHTML($tpl);
	}
}

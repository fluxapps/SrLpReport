<?php

namespace srag\CommentsUI\SrLpReport\Comment;

/**
 * Interface FactoryInterface
 *
 * @package srag\CommentsUI\SrLpReport\Comment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface FactoryInterface {

	/**
	 * @return Comment
	 */
	public function newInstance(): Comment;
}

<?php

namespace srag\CommentsUI\SrLpReport\Comment;

use srag\DIC\SrLpReport\DICTrait;

/**
 * Class Repository
 *
 * @package srag\CommentsUI\SrLpReport\Comment
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository {

	use DICTrait;
	/**
	 * @var self
	 */
	protected static $instance = null;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Repository constructor
	 */
	private function __construct() {

	}


	/**
	 * @param Comment $comment
	 */
	public function deleteComment(Comment $comment)/*: void*/ {
		$comment->delete();
	}


	/**
	 * @return Factory
	 */
	public function factory(): Factory {
		return Factory::getInstance();
	}


	/**
	 * @param int $id
	 *
	 * @return Comment|null
	 */
	public function getCommentById(int $id)/*: ?Comment*/ {
		/**
		 * @var Comment|null $comment
		 */

		$comment = Comment::where([ "id" => $id ])->first();

		return $comment;
	}


	/**
	 * @return Comment[]
	 */
	public function getComments(): array {
		/**
		 * @var Comment[] $comments
		 */

		$comments = Comment::orderBy("updated_timestamp", "desc")->get();

		return $comments;
	}


	/**
	 * @param int $report_obj_id
	 * @param int $report_user_id
	 *
	 * @return Comment[]
	 */
	public function getCommentsForReport(int $report_obj_id, int $report_user_id): array {
		/**
		 * @var Comment[] $comments
		 */

		$comments = Comment::where([
			"report_obj_id" => $report_obj_id,
			"report_user_id" => $report_user_id
		])->orderBy("updated_timestamp", "desc")->get();

		return $comments;
	}


	/**
	 * @param int|null $report_obj_id
	 *
	 * @return Comment[]
	 */
	public function getCommentsForCurrentUser(/*?int*/
		$report_obj_id = null): array {
		/**
		 * @var Comment[] $comments
		 */

		$where = [
			"report_user_id" => self::dic()->user()->getId(),
			"is_shared" => true
		];

		if (!empty($report_obj_id)) {
			$where["report_obj_id"] = $report_obj_id;
		}

		$comments = Comment::where($where)->orderBy("updated_timestamp", "desc")->get();

		return $comments;
	}


	/**
	 * @param Comment $comment
	 */
	public function storeInstance(Comment $comment)/*: void*/ {
		$time = time();

		if (empty($comment->getId())) {
			$comment->setCreatedTimestamp($time);
			$comment->setCreatedUserId(self::dic()->user()->getId());
		}

		$comment->setUpdatedTimestamp($time);
		$comment->setUpdatedUserId(self::dic()->user()->getId());

		$comment->store();
	}
}
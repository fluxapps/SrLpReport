<?php

namespace srag\CustomInputGUIs\SrLpReport\LearningProgressPie;

use srag\DIC\SrLpReport\DICTrait;

/**
 * Class LearningProgressPie
 *
 * @package srag\CustomInputGUIs\SrLpReport\LearningProgressPie
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class LearningProgressPie {

	use DICTrait;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance(): self {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @return CountLearningProgressPie
	 */
	public function count(): CountLearningProgressPie {
		return new CountLearningProgressPie();
	}


	/**
	 * @return ObjIdsLearningProgressPie
	 */
	public function objIds(): ObjIdsLearningProgressPie {
		return new ObjIdsLearningProgressPie();
	}


	/**
	 * @return UsrIdsLearningProgressPie
	 */
	public function usrIds(): UsrIdsLearningProgressPie {
		return new UsrIdsLearningProgressPie();
	}
}

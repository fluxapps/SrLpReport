<?php

namespace srag\DIC\SrLpReport\DIC;

use srag\DIC\SrLpReport\Database\DatabaseDetector;
use srag\DIC\SrLpReport\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\SrLpReport\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDIC implements DICInterface {

	/**
	 * AbstractDIC constructor
	 */
	protected function __construct() {

	}


	/**
	 * @inheritdoc
	 */
	public function database(): DatabaseInterface {
		return DatabaseDetector::getInstance($this->databaseCore());
	}
}

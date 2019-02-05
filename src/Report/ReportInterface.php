<?php

namespace srag\Plugins\SrLpReport\Report;

/**
 * Interface Report
 *
 * @package srag\Plugins\SrLpReport\Report
 *
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
interface ReportInterface {

	/**
	 * @return int
	 */
	public function getRepObjType(): int;


	/**
	 * @return int
	 */
	public function getRepUserType(): int;


	/**
	 * @return string
	 */
	public function getLinkTarget(): string;


	/**
	 *
	 */
	public function getGuiObject();
}

<?php

namespace srag\Plugins\SrLpReport\ReportTableGUI;

use srag\Plugins\SrLpReport\Report\ReportFactory;

/**
 * Class SingleObjectAllUserTableGUI
 *
 * @package srag\Plugins\SrLpReport\ReportTableGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SingleObjectAllUserTableGUI extends AbstractReportTableGUI {

	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId('srcrslp_usrs');
		$this->setPrefix('srcrslp_usrs');
	}


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false): string {
		switch ($column) {
			case "login":
				if ($raw_export) {
					return $row[$column];
				}

				// TODO: RefId should be a field in the data set!
				return $this->getLinkDetailView($row[$column], $this->ref_id, $row['usr_id']);
				break;
		}

		return parent::getColumnValue($column, /*array*/
			$row, /*bool*/
			$raw_export);
	}


	/**
	 * @param string $link_title
	 * @param int    $ref_id
	 * @param int    $user_id
	 */
	private function getLinkDetailView(string $link_title, int $ref_id, int $user_id) {
		$report = self::ilias()->reportRefIdUserId($ref_id, $user_id, ReportFactory::REPORT_VIEW_TYPE_MATRIX);

		// TODO: is there a Representation Classes for easy generating links with target, title?
		return '<a href="' . $report->getLinkTarget() . '">' . $link_title . "</a>";
	}
}

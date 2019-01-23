<?php
use srag\Plugins\SrLpReport\ReportTableGUI\AbstractReportTableGUI;
use srag\Plugins\SrLpReport\Report\ReportFactory;

/**
 * Class SingleObjectAllUserTableGUI
 *
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 */

class SingleObjectAllUserTableGUI  extends AbstractReportTableGUI
{

	protected function initId() {
		$this->setId('srcrslp_usrs');
		$this->setPrefix('srcrslp_usrs');
	}

	protected function getColumnValue($column, /*array*/
		$row, /*bool*/
		$raw_export = false) {


		switch ($column) {
			case "login":
				//ToDo RefId should be a field in the data set!
				return $this->getLinkDetailView($row[$column],$this->ref_id,$row['usr_id']);
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
	private function getLinkDetailView(string $link_title,int $ref_id,int $user_id) {
		$report = self::ilias()->reportRefIdUserId($ref_id,$user_id,ReportFactory::REPORT_VIEW_TYPE_MATRIX);

		//ToDo is there a Representation Classes for easy generating links with target, title?
		return '<a href="'.$report->getLinkTarget().'">'.$link_title."</a>";
	}




}
?>
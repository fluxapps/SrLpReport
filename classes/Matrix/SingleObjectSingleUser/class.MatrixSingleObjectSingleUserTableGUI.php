<?php

/**
 * Class MatrixSingleObjectSingleUserTableGUI
 *
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 */

class MatrixSingleObjectSingleUserTableGUI extends AbstractMatrixTableGUI
{


	protected function initData()
	{
		$this->filter['usr_id'] = $_GET['usr_id'];
		parent::initData();
	}


	protected function initId() {
		$this->setId('srrep_msu');
		$this->setPrefix('srrep_msu');
	}

	protected function initFilterFields() {
		return array();
	}

}
?>
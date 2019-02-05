<?php
require_once __DIR__ . "/../../../vendor/autoload.php";

/**
 * Class MatrixSingleObjectSingleUserGUI
 *
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy MatrixSingleObjectSingleUserGUI: ilSrLpReportGUI
 */
class MatrixSingleObjectSingleUserGUI extends AbstractMatrixGUI {

	const LP_STATUS_COLOR = [
		ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM => "#ddd",
		ilLPStatus::LP_STATUS_IN_PROGRESS_NUM => "#F6D842",
		ilLPStatus::LP_STATUS_COMPLETED_NUM => "#60B060",
		ilLPStatus::LP_STATUS_FAILED => "#B06060"
	];
	const CLASS_PLUGIN_BASE_GUI = ilSrLpReportGUI::class;
	const CLASS_PLUGIN_ROUTER_GUI = 'ilUIPluginRouterGUI';


	/**
	 * SummaryGUI constructor
	 */
	public function __construct() {
		$this->initJS();
		parent::__construct();

		$this->setTabs();
	}


	function getTableGuiClassName(): string {
		return MatrixSingleObjectSingleUserTableGUI::class;
	}


	/**
	 *
	 */
	protected function initJS()/*: void*/ {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/node_modules/d3/dist/d3.min.js");
	}


	public function listUsers() {
		$table_class_name = $this->getTableGuiClassName();

		$this->table = new $table_class_name($this, self::dic()->ctrl()->getCmd());

		self::dic()->mainTemplate()->setContent($this->table->getHTML());

		if ($this->getRightColumn()) {
			self::dic()->mainTemplate()->setRightContent($this->getRightColumn());
		}

		self::output()->output("", true);
	}


	/**
	 * Render status data as needed for summary list (based on grouped values)
	 *
	 * @param    array $status_data status data
	 * @param    int   $absolute    overall number of entries
	 *
	 * @return    string
	 */
	protected function getLearningProgressJson(array $status_data = NULL, $absolute = 0): string {

		self::dic()->language()->loadLanguageModule('trac');
		$json_string = "";

		foreach ($status_data as $status_number => $status_count) {

			if ($status_count == 0) {
				continue;
			}

			$array_status = array();

			$arr_status['user_count'] = $status_count;
			$arr_status['reached'] = $status_count;
			$arr_status['reached_label'] = $status_count;
			$arr_status['label'] = ilLearningProgressBaseGUI::_getStatusText($status_number);
			$arr_status['color'] = self::LP_STATUS_COLOR[$status_number];
			$arr_status['absolute'] = $absolute;

			$json_string[] = json_encode($arr_status);
		}

		return implode(',', $json_string);
	}


	/**
	 * @param string $json_status
	 * @param int    $row_identifier
	 * @param int    $user_total
	 *
	 * @return string
	 * @throws \srag\DIC\SrLPReport\Exception\DICException
	 * @throws ilTemplateException
	 */
	public function getLearningProgressRepresentation($json_status = "", $row_identifier = 0, $user_total = 0) {

		$tpl_learning_progress_chart = self::plugin()->template("LearningProgress/chart.html", false, false);

		$tpl_learning_progress_chart->setVariable("ROW_IDENTIFIER", $row_identifier);
		$tpl_learning_progress_chart->setVariable("TOTAL", $user_total);
		$tpl_learning_progress_chart->setVariable("JSON_STATUS", $json_status);

		return self::output()->getHTML($tpl_learning_progress_chart);
	}


	public function getRightColumn() {
		$data = $this->table->getData();

		$status_data[ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM] = 0;
		$status_data[ilLPStatus::LP_STATUS_IN_PROGRESS_NUM] = 0;
		$status_data[ilLPStatus::LP_STATUS_COMPLETED_NUM] = 0;

		$absolute = 0;
		foreach ($data as $row) {
			$status_data[$row['status']] ++;
			$absolute ++;
		}

		$html = "";
		if ($absolute > 0) {
			$lp_json = $this->getLearningProgressJson($status_data, $absolute);
			$html = $this->getLearningProgressRepresentation($lp_json, $_GET['usr_id'], $absolute);
			$html .= "<br/>";
			//$tpl->setVariable("HEADER",$this->getLearningProgressRepresentation($lp_json));
		}

		$pub_profile = new ilPublicUserProfileGUI($_GET['usr_id']);
		$html .= $pub_profile->getEmbeddable() . "<br/>";

		$html .= ilSrLpReportGUI::getLegendHTML();

		return $html;
	}


	public function setTabs()/*: void*/ {
		self::dic()->tabs()->clearTargets();

		self::dic()->ctrl()->saveParameterByClass(SingleObjectAllUserGUI::class, 'ref_id');
		self::dic()->ctrl()->saveParameterByClass(SingleObjectAllUserGUI::class, 'details_id');
		self::dic()->ctrl()->setParameterByClass(SingleObjectAllUserGUI::class, 'sr_rp', 1);

		self::dic()->tabs()->setBackTarget(self::dic()->language()->txt("back"), self::dic()->ctrl()->getLinkTargetByClass([
				self::CLASS_PLUGIN_ROUTER_GUI,
				self::CLASS_PLUGIN_BASE_GUI,
				SingleObjectAllUserGUI::class
			]));
	}
}

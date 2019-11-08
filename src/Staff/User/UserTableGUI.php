<?php

namespace srag\Plugins\SrLpReport\Staff\User;

use ilAdvancedSelectionListGUI;
use ilCourseParticipants;
use ilDateTime;
use ilLPStatus;
use ilMStListCourse;
use ilMyStaffGUI;
use ilObjCourseGUI;
use ilObject2;
use ilPublicUserProfileGUI;
use ilRepositoryGUI;
use ilSelectInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\SrLpReport\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrLpReport\Report\ReportGUI;
use srag\Plugins\SrLpReport\Report\Reports;
use srag\Plugins\SrLpReport\Staff\AbstractStaffTableGUI;
use srag\Plugins\SrLpReport\Staff\StaffGUI;

/**
 * Class UserTableGUI
 *
 * @package srag\Plugins\SrLpReport\Staff\User
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserTableGUI extends AbstractStaffTableGUI {

	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/ $column, /*array*/ $row, /*int*/ $format = self::DEFAULT_FORMAT): string {
		switch (true) {
			case $column === "crs_title":
				$column = $row[$column];
				if (!$format) {
					$course_gui = new ilObjCourseGUI();
					 self::dic()->ctrl()->setParameter($course_gui,'ref_id',$row["crs_ref_id"]);
					$column = self::output()->getHTML(self::dic()->ui()->factory()->link()->standard($column, self::dic()->ctrl()->getLinkTargetByClass([ilRepositoryGUI::class,ilObjCourseGUI::class])));
				}
				break;

			case $column === "usr_reg_status":
				$column = ilMStListCourse::getMembershipStatusText($row[$column]);
				break;

			case $column === "usr_lp_status":
				if (!$format) {
					$column = StaffGUI::getUserLpStatusAsHtml($row["ilMStListCourse"]);
				} else {
					$column = StaffGUI::getUserLpStatusAsText($row["ilMStListCourse"]);
				}
				break;

			case $column === "learning_progress_objects":
				if (!$format) {
					$column = self::output()->getHTML($row["pie"]);
				} else {
                    $column = "";
				}
				break;

            case $column === "learning_progress_objects_count":
                return $column = $row["pie"]->getData()["count"];

            case strpos($column, "learning_progress_objects_") === 0:
                $status = intval(substr($column, strlen("learning_progress_objects_")));

                $column = $row["pie"]->getData()["data"][$status]["value"];
                break;

			//TODO Performance
			case $column === "condition_passed":
				$course_participant = new ilCourseParticipants(ilObject2::_lookupObjectId($row["crs_ref_id"]));
				$passed_info = $course_participant->getPassedInfo($row["usr_id"]);
				if(is_array($passed_info)) {
					/**
					 * @var ilDatetime $datetime
					 */
					$datetime = $passed_info['timestamp'];
					$column = $datetime->get(IL_CAL_DATE);
				} else {
					$column = "";
				}
				break;

			default:
				$column = $row[$column];
				break;
		}

		return strval($column);
	}


	/**
	 * @inheritdoc
	 */
	public function getSelectableColumns2(): array {
		$columns = [
			"crs_title" => [
				"default" => true,
				"txt" => self::dic()->language()->txt("title")
			],
			"usr_reg_status" => [
				"default" => true,
				"txt" => self::dic()->language()->txt("member_status")
			],
			"usr_lp_status" => [
				"default" => true,
				"txt" => self::dic()->language()->txt("trac_learning_progress")
			],
			"condition_passed" => [
			"default" => true,
			"txt" => self::dic()->language()->txt("condition_passed")
		]


		];

        if ($this->getExportMode()) {
            $columns["learning_progress_objects_count"] = [
                "default" => true,
                "txt"     => self::dic()->language()->txt("total") . " " . self::dic()->language()->txt("objects")
            ];
            foreach (self::customInputGUIs()->learningProgressPie()->objIds()->getTitles() as $status => $title) {
                $columns["learning_progress_objects_" . $status] = [
                    "default" => true,
                    "txt"     => $title
                ];
            }
        } else {
            $columns["learning_progress_objects"] = [
                "default" => true,
                "txt" => self::dic()->language()->txt("trac_learning_progress") . " " . self::dic()->language()->txt("objects")
            ];
        }

		$no_sort = [
			"condition_passed",
			"learning_progress_objects"
		];

		foreach ($columns as $id => &$column) {
			$column["id"] = $id;
			$column["default"] = ($column["default"] === true);
			$column["sort"] = (!in_array($id, $no_sort));
		}

		return $columns;
	}


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		parent::initColumns();

		$this->addColumn(self::dic()->language()->txt("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setExternalSorting(false);
		$this->setExternalSegmentation(false);

		$this->setDefaultOrderField("crs_title");
		$this->setDefaultOrderDirection("asc");

		$this->determineLimit();
		$this->determineOffsetAndOrder();

		$data = self::ilias()->staff()->user()->getData(self::reports()
			->getUsrId(), $this->getFilterValues2(), "","", 0, 0);

        $data["data"] = array_map(function (array $row) : array {
            $row["pie"] = self::customInputGUIs()->learningProgressPie()->objIds()->withObjIds($row["learning_progress_objects"])->withUsrId($row["usr_id"]);

            if ($this->getExportMode()) {
                $row["pie"] = $row["pie"]->withShowEmpty(true);
            }

            return $row;
        }, $data["data"]);

		$this->setMaxCount($data["max_count"]);
		$this->setData($data["data"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function initFilterFields()/*: void*/ {
		$this->filter_fields = [
			"crs_title" => [
				PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
				"setTitle" => $this->dic()->language()->txt("title")
			],
			"memb_status" => [
				PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
				PropertyFormGUI::PROPERTY_OPTIONS => [
					0 => self::dic()->language()->txt("trac_all"),
					ilMStListCourse::MEMBERSHIP_STATUS_REQUESTED => self::dic()->language()->txt("mst_memb_status_requested"),
					ilMStListCourse::MEMBERSHIP_STATUS_WAITINGLIST => self::dic()->language()->txt("mst_memb_status_waitinglist"),
					ilMStListCourse::MEMBERSHIP_STATUS_REGISTERED => self::dic()->language()->txt("mst_memb_status_registered")
				],
				"setTitle" => self::dic()->language()->txt("member_status")
			],
			"lp_status" => [
				PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
				PropertyFormGUI::PROPERTY_OPTIONS => [
					0 => self::dic()->language()->txt("trac_all"),
					ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_NOT_ATTEMPTED),
					ilLPStatus::LP_STATUS_IN_PROGRESS_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_IN_PROGRESS),
					ilLPStatus::LP_STATUS_COMPLETED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_COMPLETED)
					//ilLPStatus::LP_STATUS_FAILED_NUM + 1 => self::dic()->language()->txt(ilLPStatus::LP_STATUS_FAILED)
				],
				"setTitle" => self::dic()->language()->txt("trac_learning_progress")
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("srcrslp_staff_user");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		self::dic()->mainTemplate()->setTitle(self::dic()->language()->txt("my_staff") . " " . self::dic()->objDataCache()->lookupTitle(self::reports()->getUsrId()));
	}


	/**
	 * @inheritdoc
	 */
	protected function extendsActionsMenu(ilAdvancedSelectionListGUI $actions, array $row)/*: void*/ {
		self::dic()->ctrl()->setParameter($this->parent_obj, Reports::GET_PARAM_REF_ID, $row["crs_ref_id"]);

		$actions->setId($row["crs_obj_id"]);
	}


	/**
	 * @inheritdoc
	 */
	protected function getRightHTML(): string {
		return self::output()->getHTML([
			(new ilPublicUserProfileGUI(self::reports()->getUsrId()))->getEmbeddable(),
			"<br>",
			ReportGUI::getLegendHTML()
		]);
	}
}

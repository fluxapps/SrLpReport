<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrLpReport\Util\LibraryLanguageInstaller;
use srag\Plugins\SrLpReport\Comment\Comment\Comment;
use srag\Plugins\SrLpReport\Config\Config;
use srag\Plugins\SrLpReport\Utils\SrLpReportTrait;
use srag\RemovePluginDataConfirm\SrLpReport\PluginUninstallTrait;

/**
 * Class ilSrLpReportPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLpReportPlugin extends ilUserInterfaceHookPlugin {

	use PluginUninstallTrait;
	use SrLpReportTrait;
	const PLUGIN_ID = "srlprep";
	const PLUGIN_NAME = "SrLpReport";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = SrLpReportRemoveDataConfirm::class;
	/**
	 * @var self|null
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
	 * ilSrLpReportPlugin constructor
	 */
	public function __construct() {
		parent::__construct();
	}


	/**
	 * @return string
	 */
	public function getPluginName(): string {
		return self::PLUGIN_NAME;
	}


	/**
	 * @inheritdoc
	 */
	public function updateLanguages($a_lang_keys = null) {
		parent::updateLanguages($a_lang_keys);

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
			. "/../vendor/srag/removeplugindataconfirm/lang")->updateLanguages();

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__ . "/../vendor/srag/commentsui/lang")
			->updateLanguages();

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
			. "/../vendor/srag/custominputguis/lang")->updateLanguages();
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(Config::TABLE_NAME, false);
		self::dic()->database()->dropTable(Comment::TABLE_NAME, false);
	}
}

<?php

use Bitrix\Main\EventManager;
use RI\CreditCalc\Config;

/**
*/
class ri_creditcalc extends CModule {
	/** @var string $MODULE_ID */
	public $MODULE_ID;

	/** @var null $MODULE_VERSION */
	public $MODULE_VERSION;

	/** @var null $MODULE_VERSION_DATE */
	public $MODULE_VERSION_DATE;

	/** @var null $MODULE_NAME */
	public $MODULE_NAME;

	/** @var null $MODULE_DESCRIPTION */
	public $MODULE_DESCRIPTION;

	/** @var Config|null $config */
	private $config;

	/** @var null $request */
	private $request;

	/** @var \Bitrix\Main\EventManager */
	private $em;

	private $handlers;

	/**
	* Function construct
	*/
	public function __construct() {

		require_once __DIR__ . "/../Config.php";

		$config = Config::getInstance();
		$this->config = $config;
		$this->MODULE_ID = $config->getId();

		$this->request = \Bitrix\Main\Context::getCurrent()->getRequest();

		$arModuleVersion = [];

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path . "/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = $config->getName();
		$this->MODULE_DESCRIPTION = $config->getDescription();

		$this->PARTNER_NAME = $config->getPartnerName();
		$this->PARTNER_URI = $config->getPartnerUri();

		$this->em = \Bitrix\Main\EventManager::getInstance();

        $this->globalEvents = [
            [
                'main',
                'OnPageStart',
                \RI\CreditCalc\EventHandler::class,
                'onPageStart'
            ],
        ];
	}


    public function InstallEvents() {
        $eventManager = EventManager::getInstance();
        foreach($this->globalEvents as $handler){
            $eventManager->registerEventHandler($handler[0], $handler[1], $this->MODULE_ID, $handler[2], $handler[3]);
        }
    }

    public function UnInstallEvents() {
        $eventManager = EventManager::getInstance();
        foreach ($this->globalEvents as $handler) {
            $eventManager->unRegisterEventHandler($handler[0], $handler[1], $this->MODULE_ID, $handler[2], $handler[3]);
        }
    }

	/**
	* @return null
	*/
	public function DoInstall() {
		$this->InstallEvents();
		RegisterModule($this->MODULE_ID);

		return null;
	}

	/**
	* @return null
	*/
	public function DoUninstall() {
		$this->UnInstallEvents();
		UnRegisterModule($this->MODULE_ID);

		return null;
	}
}

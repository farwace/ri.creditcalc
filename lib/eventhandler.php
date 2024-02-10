<?php
namespace RI\CreditCalc;

use Bitrix\Main\Context;
use Bitrix\Main\EventManager;
use Bitrix\Main\HttpRequest;


class EventHandler {
	use Singleton;

	private HttpRequest $request;

	public function createInstance(){
		$this->request = Context::getCurrent()->getRequest();
	}

	public static function onPageStart(){
		$instance = static::getInstance();
		$em = EventManager::getInstance();

        //$em->addEventHandler('form', 'onBeforeResultAdd', [$instance, 'onBeforeResultAddHandler']);
	}


//    public function onBeforeResultAddHandler($formId, $arFields, $arValues){
//
//    }


}

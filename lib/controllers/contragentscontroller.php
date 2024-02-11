<?php

namespace RI\CreditCalc\Controllers;

use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Component;
use Bitrix\Main\Page\Asset;
use RI\CreditCalc\Services\ContrAgentsService;

class ContragentsController extends Controller
{
    protected function getDefaultPreFilters(): array
    {
        return [];
    }

    protected function getDefaultPostFilters(): array
    {
        return [];
    }

    public function getContragentAction()
    {
        $query = $this->request->get('query');
        /** @var ContrAgentsService $contrAgentsService */
        $contrAgentsService = ServiceLocator::getInstance()->get('ri.contragents');

        $cache = Application::getInstance()->getManagedCache();
        $cacheKey = 'getContragent-' . $query;
        if($cache->read(3600, $cacheKey)){
            $contragentResult = $cache->get($cacheKey);
        }
        else{
            $contragentResult = $contrAgentsService->getContrAgent($query);
            $cache->set($cacheKey, $contragentResult);
        }

        if(!$contragentResult->isSuccess()){
            $cache->clean($cacheKey);
            $this->addErrors($contragentResult->getErrors());
        }

        return $contragentResult->getData();
    }

    public function addRequestAction()
    {
        $arRequest = $this->request->toArray();


    }

}

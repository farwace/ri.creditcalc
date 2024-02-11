<?php

namespace RI\CreditCalc\Controllers;

use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Component;
use Bitrix\Main\Error;
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

        $phone = $arRequest['phone'] ?: '';
        $company = $arRequest['company'] ?: '';
        $inn = $arRequest['inn'] ?: '';
        $period = $arRequest['period'] ?: '';
        $summ = $arRequest['summ'] ?: '';

        if(empty($arRequest['phone'])){
            $this->addError(new Error('Не указаны обязательные поля'));
            return [];
        }

        $iblockId = \COption::GetOptionString('ri.creditcalc', 'iblock_id');
        if(!empty($iblockId) && (int)$iblockId > 0){

            $propertyValues = [];

            $phonePropId = \COption::GetOptionString('ri.creditcalc', 'phone_id');
            if(!empty($phonePropId) && (int) $phonePropId > 0){
                $propertyValues[(int) $phonePropId] = $phone;
            }

            $companyPropId = \COption::GetOptionString('ri.creditcalc', 'company_id');
            if(!empty($companyPropId) && (int) $companyPropId > 0){
                $propertyValues[(int) $companyPropId] = $company;
            }

            $innPropId = \COption::GetOptionString('ri.creditcalc', 'inn_id');
            if(!empty($innPropId) && (int) $innPropId > 0){
                $propertyValues[(int) $innPropId] = $inn;
            }

            $periodPropId = \COption::GetOptionString('ri.creditcalc', 'period_id');
            if(!empty($periodPropId) && (int) $periodPropId > 0){
                $propertyValues[(int) $periodPropId] = $period;
            }

            $summPropId = \COption::GetOptionString('ri.creditcalc', 'summ_id');
            if(!empty($summPropId) && (int) $summPropId > 0){
                $propertyValues[(int) $summPropId] = $summ;
            }

            $el = new \CIBlockElement();
            $el->Add([
                'IBLOCK_ID' => (int)$iblockId,
                'NAME' => 'Заявка ' . $company . ' от ' . date('d.m.Y'),
                'PROPERTY_VALUES' => $propertyValues
            ]);
        }


        return [];

    }

}

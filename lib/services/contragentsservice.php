<?php

namespace RI\CreditCalc\Services;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Error;
use Bitrix\Main\Result;

class ContrAgentsService{
    const MODULE_ID = 'ri.creditcalc';

    public function getContrAgent($query){
        return $this->sendQuery(
            'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party',
            [
                'query' => $query
            ]
        );
    }

    private function isValidInn($inn){
        if((int)$inn != $inn){
            return false;
        }
        $strLen = strlen((string)$inn);
        if( $strLen != 10 && $strLen != 12 ){
            return false;
        }

        return true;
    }

    private function sendQuery($apiUrl, $data):Result
    {
        $result = new Result();
        $apiKey = \COption::GetOptionString(self::MODULE_ID, 'api_key', '');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $arHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token ' . $apiKey,
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arHeaders);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $res = curl_exec($ch);

        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            switch ($http_code) {
                case 200:  # OK
                    $result->setData(json_decode($res, true));
                    break;
                default:
                    $result->addError(new Error('Неизвестный код ответа', $http_code));
                    break;
            }
        }
        else{
            $result->addError(new Error('Ошибка отправки запроса', 0));
        }

        return $result;
    }
}
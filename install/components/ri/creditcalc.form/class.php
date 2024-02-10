<?php
/** @var CMain $APPLICATION */

namespace RI\Components;

use Bitrix\Main\Loader;

class CreditCalcForm extends \CBitrixComponent{
    public function executeComponent()
    {
        global $APPLICATION;
        if(!Loader::includeModule('ri.creditcalc')){
            $APPLICATION->ThrowError('Модуль не установлен');
        }

        $this->includeComponentTemplate();
    }
}
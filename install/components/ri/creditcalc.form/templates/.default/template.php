<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
CJSCore::Init(array("fx"));
\Bitrix\Main\UI\Extension::load("ui.vue3");
$moduleId = 'ri.creditcalc';
$uniqId = uniqid('creditCalc');

$jsParams = [
    'elId' => $uniqId,
    'summMin' => COption::GetOptionInt($moduleId, 'credit_summ_min', 1000000),
    'summMax' => COption::GetOptionInt($moduleId, 'credit_summ_max', 30000000),
    'summDefault' => COption::GetOptionInt($moduleId, 'credit_summ_default', 3000000),
    'periodMin' => COption::GetOptionInt($moduleId, 'credit_period_min', 1),
    'periodMax' => COption::GetOptionInt($moduleId, 'credit_period_max', 10),
    'periodDefault' => COption::GetOptionInt($moduleId, 'credit_period_default', 3),
    'percent' => COption::GetOptionInt($moduleId, 'credit_percent', 12),
];
?>


<script type="text/template" id="creditcalc-ranges-template">
    <div class="ri__ranges">
        <div class="ri__ranges__item">
            <div class="ri__ranges__item__label">
                Сумма кредита
            </div>
            <div class="ri__ranges__item__input">
                <input type="text" v-model="currentSumm" :min="summMin" :max="summMax" :step="100000">
            </div>
            <div class="ri__ranges__item__range">
                <input type="range" v-model="currentSumm" :min="summMin" :max="summMax" :step="100000">
            </div>
        </div>
        <div class="ri__ranges__item">
            <div class="ri__ranges__item__label">
                Срок кредита
            </div>
            <div class="ri__ranges__item__input">
                <input type="text" v-model="currentPeriod" :min="periodMin" :max="periodMax" :step="1">
            </div>
            <div class="ri__ranges__item__range">
                <input type="range" v-model="currentPeriod" :min="periodMin" :max="periodMax" :step="1">
            </div>
        </div>

        <div class="ri__ranges__buttons">
            <div class="ri__btn btn btn--green" @click="$emit('secondStep')">
                Получить одобрение
            </div>
        </div>
    </div>
</script>
<script type="text/template" id="creditcalc-start-info-template">
    <div class="ri__info__body">
        <div class="ri__info__body__top">
            <div class="ri__info__body__top__item">
                <div class="ri__info__body__top__item__value">
                    {{ percent }}%
                </div>
                <div class="ri__info__body__top__item__desc">
                    Ставка
                </div>
            </div>
            <div class="ri__info__body__top__item">
                <div class="ri__info__body__top__item__value">
                    {{ paymentPerMonthFormatted }} ₽
                </div>
                <div class="ri__info__body__top__item__desc">
                    Ежемесячный платёж
                </div>
            </div>
        </div>
        <div class="ri__info__body__bottom">
            Рассчет произведён по усредненной процентной ставке с учетом льготных условий.
        </div>
    </div>
</script>


<div id="<?=$uniqId?>" class="ri">
    <div class="ri__creditcalc">
        <div class="ri__creditcalc__title">Рассчитать платеж</div>
        <div class="ri__creditcalc__body">
            <div class="ri__creditcalc__body__ranges">
                <creditcalc-ranges
                        :summ-min="summMin"
                        :summ-max="summMax"
                        :summ="summ"
                        :period-min="periodMin"
                        :period-max="periodMax"
                        :period="period"
                        v-on:changed-summ="setSumm"
                        v-on:changed-period="setPeriod"
                        v-on:second-step="secondStep"
                />
            </div>
            <div class="ri__creditcalc__body__info">
                <creditcalc-start-info :percent="percent" :payment-per-month="paymentPerMonth"/>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function (){
        RI.CreditCalc(<?=CUtil::PhpToJSObject($jsParams)?>)
    })
</script>
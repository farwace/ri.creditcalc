<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var string $templateFolder */
CJSCore::Init(array("fx"));
define("VUEJS_DEBUG", true);
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
                <input type="text" @input="checkInputSumm" @change="prepareSumm" v-model="currentSummFormatted" :min="summMin" :max="summMax" :step="100000">
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
                <input type="text" readonly v-model="currentPeriodFormatted" :min="periodMin" :max="periodMax" :step="1">
            </div>
            <div class="ri__ranges__item__range">
                <input type="range" v-model="currentPeriod" :min="periodMin" :max="periodMax" :step="1">
            </div>
        </div>

        <div class="ri__ranges__buttons" v-if="!isPopup">
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
        <div class="ri__info__body__bottom" v-if="!isPopup">
            Рассчет произведён по усредненной процентной ставке с учетом льготных условий.
        </div>
    </div>
</script>

<script type="text/template" id="creditcalc-popup-template">
    <div class="ri__creditcalc__popup" v-click-outside="close">
        <div class="modal__close close" @click="$emit('close')"></div>
        <div class="ri__creditcalc__popup__title">
            {{title}}
        </div>
        <div class="ri__creditcalc__popup__subtitle" v-if="subtitle">
            {{subtitle}}
        </div>
        <div class="ri__creditcalc__popup__body">
            <slot></slot>
        </div>
    </div>
</script>

<script type="text/template" id="creditcalc-contragent-form">
    <div class="ri__contragent-form">
        <div class="ri__contragent-form__title">Проверьте Вашу компанию сейчас</div>

        <div class="ri__contragent-form__item input-holder">
            <input :class="{'error':isError}" @input="updateQuery" v-model="query" placeholder="Введите название или ИНН" type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
            <span class="error__text">Поле не заполнено</span>
            <div class="ri__contragent-form__loading" v-if="isLoading">
                <img src="<?=$templateFolder?>/loading.svg" alt="">
            </div>
            <div class="ri__contragent-form__suggestions" :class="{'hidden':!canShowSuggestions}" v-click-outside="hideSuggestions">
                <div class="ri__contragent-form__suggestions__item" v-for="suggestion in suggestions" @click="setSelectedSuggestion(suggestion)">
                    <div class="ri__contragent-form__suggestions__item__name" :class="{'liquidated':suggestion.data.state.status==='LIQUIDATED'}">
                        {{ suggestion.value }}
                    </div>
                    <div class="ri__contragent-form__suggestions__item__desc">
                        <div v-html="suggestion.data.inn"></div>
                        <div v-html="suggestion.data.address.data.city_with_type"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ri__contragent-form__terms">
            Ввод данных подтверждает ваше согласие на <a href="/policy/" target="_blank">обработку персональных данных</a>
        </div>

        <div class="ri__contragent-form__buttons">
            <div class="">
                <span class="btn btn--green" @click="tryToNextStep">Узнать условия</span>
            </div>
            <div class="">
                <span class="btn btn--green-border" data-call="callback">Перезвоните мне</span>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="creditcalc-total-form-template">
    <div class="ri__final">
        <div class="ri__final__item">
            <div class="ri__final__item__title">
                <div class="ri__final__item__title__name">
                    {{ suggestion.value }}
                </div>
                <div class="ri__final__item__title__desc">
                    <div class="ri__final__item__title__desc__summ">
                        {{ summMinFormated }} - {{ summMaxFormated }} ₽
                    </div>
                    <div class="ri__final__item__title__desc__percent">
                        {{ percent }}% годовых
                    </div>
                </div>
            </div>
            <div class="ri__final__item__body">
                <div class="ri__final__item__box" v-if="companyStatus">
                    <div class="ri__final__item__box__title">
                        Статус <span v-html="companyType"></span>
                    </div>
                    <div class="ri__final__item__box__value ri__final__item__box__value__big">
                        <span v-html="companyStatus"></span>
                    </div>
                    <div class="ri__final__item__box__value" v-if="registerDate">
                        регистрация <span v-html="registerDate"></span>
                    </div>
                </div>

                <div class="ri__final__item__box">
                    <div class="ri__final__item__box__title" v-if="companyType">
                        Реквизиты <span v-html="companyType"></span>
                    </div>
                    <div class="ri__final__item__box__value">
                        <p v-if="companyAddress"><span class="ri__final__item__requisites__desc__address" v-html="companyAddress"></span></p>
                        <p v-if="inn">ИНН: <span v-html="inn"></span><span v-if="ogrn" v-html="' / ОГРН: ' + ogrn"></span></p>
                    </div>
                </div>

                <div class="ri__final__item__box" v-if="boss">
                    <div class="ri__final__item__box__title">
                        О руководителе
                    </div>
                    <div class="ri__final__item__box__value ri__final__item__box__value__big">
                        <span v-html="boss"></span>
                    </div>
                    <div class="ri__final__item__box__value" v-if="bossPost">
                        <span v-html="bossPost"></span>
                    </div>
                </div>

                <div class="ri__final__item__box">
                    <div class="ri__final__item__box__title">Условия кредита</div>
                    <ul class="ri__final__item__box__ul">
                        <li>Деньги за 1 день</li>
                        <li>По 2-м документам</li>
                        <li>Досрочное погашение</li>
                    </ul>
                </div>

                <div class="ri__final__item__box">
                    <div class="ri__final__item__box__title">Бесплатно</div>
                    <div class="ri__final__item__box__value ri__final__item__box__value__big">Отказ от страховки</div>
                    <div class="ri__final__item__box__value">экономия до 30% от суммы кредита</div>
                </div>
            </div>

        </div>
        <div class="ri__final__item ri__final__item__right">
            <div class="ri__final__item__ranges">
                <slot name="ranges"></slot>
            </div>
            <div class="ri__final__item__info">
                <slot name="info"></slot>
            </div>
            <div class="ri__final__item__form">
                <slot name="form"></slot>
            </div>

        </div>
    </div>
</script>

<script type="text/template" id="creditcalc-phone-form-template">
    <div class="ri__phone-form" novalidate="">
        <div class="ri__phone-form__title">Оставьте свой телефон</div>
        <div class="ri__phone-form__desc">
            <p>Чтобы сохранить предварительное одобрение кредита</p>
        </div>

        <div class="ri__phone-form__input input-holder">
            <input :class="{error:phoneError}" v-mask="'+7 (000) 000 00-00'" v-model="phone" placeholder="Введите телефон" type="tel" autocomplete="off" name="phone" required="" inputmode="">
            <span class="error__text">Поле не заполнено</span>
            <div class="ri__contragent-form__loading" v-if="isLoading">
                <img src="<?=$templateFolder?>/loading.svg" alt="">
            </div>
        </div>

        <div class="ri__phone-form__terms">
            Ввод номера телефона подтверждает ваше согласие на <a href="/policy/" target="_blank">обработку персональных данных</a>
        </div>

        <div class="ri__phone-form__buttons">
            <div>
                <span class="ri__btn btn btn--green" @click="trySubmit" :class="{disabled: isLoading}">Оставить телефон</span>
            </div>
        </div>
    </div>
</script>

<div id="<?=$uniqId?>" class="ri">
    <div class="ri__creditcalc" :class="{'ri__creditcalc__bg':step>1, 'ri__block':true}" style="display: none">
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

        <transition name="slide-fade">
            <creditcalc-popup v-if="step > 1" @close="closePopup" :title="popupTitle" :subtitle="popupSubtitle">
                <template v-slot:default>
                    <creditcalc-contragent-form v-if="step == 2" :is-loading="isLoading" :suggestions="suggestions" @select-suggestion="setCompany" @query="findContragent" @next-step="tryLastStep"></creditcalc-contragent-form>
                    <creditcalc-total-form v-if="step == 3" :is-loading="isLoading" :suggestion="selectedSuggestion" :percent="percent" :summ-min="summMin" :summ-max="summMax">
                        <template v-slot:ranges>
                            <creditcalc-ranges
                                    :summ-min="summMin"
                                    :summ-max="summMax"
                                    :summ="summ"
                                    :period-min="periodMin"
                                    :period-max="periodMax"
                                    :period="period"
                                    :is-popup="true"
                                    v-on:changed-summ="setSumm"
                                    v-on:changed-period="setPeriod"
                                    v-on:second-step="secondStep"
                            />
                        </template>
                        <template v-slot:info>
                            <creditcalc-start-info :is-popup="true" :percent="percent" :payment-per-month="paymentPerMonth"/>
                        </template>
                        <template v-slot:form>
                            <creditcalc-phone-form :is-loading="isLoading" @submit="submitForm"></creditcalc-phone-form>
                        </template>
                    </creditcalc-total-form>
                </template>
            </creditcalc-popup>
        </transition>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function (){
        RI.CreditCalc(<?=CUtil::PhpToJSObject($jsParams)?>)
    })
</script>

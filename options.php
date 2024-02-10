<?php

use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);
$moduleId = $module_id = 'ri.creditcalc'; //$module_id - битрикс хочет
Loader::includeModule($moduleId);

global $APPLICATION;

$moduleAccess = $APPLICATION->GetGroupRight($moduleId);

if($moduleAccess >= "W"){
    $aTabs[] = [
        "DIV" => 'main_settings',
        "TAB" => 'Параметры модуля',
        "TITLE" => "Параметры модуля"
    ];
    $aTabs[] = [
        'DIV' => 'access',
        'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS')
    ];



    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['Update'] <> '' && check_bitrix_sessid()){
        COption::SetOptionString($moduleId, 'api_url', $_REQUEST['api_url']);
        COption::SetOptionString($moduleId, 'api_login', $_REQUEST['api_login']);
        COption::SetOptionString($moduleId, 'api_password', $_REQUEST['api_password']);
        COption::SetOptionString($moduleId, 'webform_id', $_REQUEST['api_password']);
    }


    $tabControl = new CAdminTabControl('tabControl', $aTabs);
    $tabControl->Begin();
    ?>

    <form action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($moduleId)?>&lang=<?=LANGUAGE_ID?>&mid_menu=1" id="ri-creditcalc-options" method="POST">

        <? $tabControl->BeginNextTab(); ?>

        <tr class="heading"><td colspan="2">Параметры формы</td></tr>

        <tr>
            <td width="40%"><b>Сумма кредита:</b></td>
            <td width="60%"><br/><br/></td>
        </tr>
        <tr>
            <td width="40%">MIN</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_summ_min', 1000000) ?>
                <input type="text" value="<?=$val?>" name="credit_summ_min" step="1000" min="10000" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%">MAX</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_summ_max', 1000000000) ?>
                <input type="text" value="<?=$val?>" name="credit_summ_max" step="1000" min="10000" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%">Предустановленное значение</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_summ_default', 14500000) ?>
                <input type="text" value="<?=$val?>" name="credit_summ_default" step="1000" min="10000" size="50">
            </td>
        </tr>

        <tr>
            <td width="40%"><b>Срок кредита, лет:</b></td>
            <td width="60%"><br/><br/></td>
        </tr>
        <tr>
            <td width="40%">MIN</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_period_min', 1) ?>
                <input type="text" value="<?=$val?>" name="credit_period_min" step="1" min="1" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%">MAX</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_period_max', 50) ?>
                <input type="text" value="<?=$val?>" name="credit_period_max" step="1" min="1" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%">Предустановленное значение</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_period_default', 7) ?>
                <input type="text" value="<?=$val?>" name="credit_period_default" step="1" min="1" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%"><b>Процентная ставка:</b></td>
            <td width="60%"><br/><br/></td>
        </tr>
        <tr>
            <td width="40%">Усредненный процент %</td>
            <td width="60%">
                <? $val = COption::GetOptionInt($moduleId, 'credit_percent', 12) ?>
                <input type="text" value="<?=$val?>" name="credit_percent" step="1" min="1" size="50">
            </td>
        </tr>


        <tr class="heading"><td colspan="2">Авторизация по API esphere</td></tr>
        <tr>
            <td width="40%">API URL</td>
            <td width="60%">
                <? $val = COption::GetOptionString($moduleId, 'api_url', '') ?>
                <input type="text" value="<?=$val?>" name="api_url" maxlength="255" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%">Логин</td>
            <td width="60%">
                <? $val = COption::GetOptionString($moduleId, 'api_login', '') ?>
                <input type="text" value="<?=$val?>" name="api_login" maxlength="255" size="50">
            </td>
        </tr>
        <tr>
            <td width="40%">Пароль</td>
            <td width="60%">
                <? $val = COption::GetOptionString($moduleId, 'api_password', '') ?>
                <input type="password" value="<?=$val?>" name="api_password" maxlength="255" size="50">
            </td>
        </tr>


        <tr class="heading"><td colspan="2">Хранение результатов</td></tr>
        <tr>
            <td width="40%">ID WEB-формы</td>
            <td width="60%">
                <? $val = COption::GetOptionString($moduleId, 'webform_id', '') ?>
                <input type="text" value="<?=$val?>" name="webform_id" maxlength="255" size="50">
            </td>
        </tr>

        <? $tabControl->BeginNextTab(); ?>
        <? require_once($_SERVER['DOCUMENT_ROOT']. '/bitrix/modules/main/admin/group_rights.php');?>
        <? $tabControl->Buttons(); ?>
        <input type="submit" class="adm-btn-green" name="Update" value="Сохранить">
        <input type="hidden" name="Update" value="Y">
        <?=bitrix_sessid_post()?>

    </form>

    <? $tabControl->End(); ?>

    <?php
}
<?php

use Bitrix\Currency\CurrencyTable;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}


Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('currency'))
{
    ShowError(Loc::getMessage('OTUS_ERROR_NOT_INSTALLED_CURRENCY'));
    return;
}

class OtusCurrencyComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($params)
    {
        return $params;
    }



    public function executeComponent()
    {

        if($this->arParams['CURRENCY_CODE']){
            $this->arResult['CURRENCY'] = CurrencyTable::getById($this->arParams['CURRENCY_CODE'])->fetch();
        }
        else {
            $this->arResult['CURRENCY'] = false;
        }


        $this->includeComponentTemplate();
    }
}
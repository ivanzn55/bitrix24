<?php

use Bitrix\Main\Loader;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


Loader::includeModule('currency');

$result = CurrencyTable::getList([
    'select' => ['CURRENCY'],
    'order'  => ['SORT' => 'ASC']
]);

$listCurrencies = [];

while ($currency = $result->fetch()) {

    $listCurrencies[$currency['CURRENCY']] = $currency['CURRENCY'];

}


$arComponentParameters = array(

    "PARAMETERS" => array(
        "CURRENCY_CODE" =>  array(
            "PARENT" => "BASE",
            "NAME"=>  Loc::getMessage("OTUS_CURRENCY_RATE_LIST"),
            "TYPE" => "LIST",
            "VALUES" => $listCurrencies,
        ),
    )
);

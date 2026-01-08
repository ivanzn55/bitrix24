<?php
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME'        => Loc::getMessage("OTUS_CURRENCY_RATE_COMPONENT_NAME"),
    'DESCRIPTION' => Loc::getMessage("OTUS_CURRENCY_RATE_COMPONENT_DESCRIPTION"),
    "SORT" => 20,
    'PATH'        => [
        'ID'    => 'otus',
        'NAME'  => 'otus',
        'CHILD' => [
            'ID'   => 'otus.currency.rate',
            'NAME' => Loc::getMessage("OTUS_CURRENCY_RATE_COMPONENT_NAME"),
        ],
    ],
];
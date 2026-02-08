<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arActivityDescription = [
    "NAME" => Loc::getMessage("LOAD_COMPANY_BY_INN_ACTIVITY_DESCR_NAME"),
    "DESCRIPTION" => Loc::getMessage("LOAD_COMPANY_BY_INN_ACTIVITY_DESCR_DESCR"),
    "TYPE" => "activity",
    "CLASS" => "LoadCompanyActivity",
    "JSCLASS" => "BizProcActivity",
    "CATEGORY" => [
        "ID" => "other",
    ],
    "RETURN" => [
        "Text" => [
            "NAME" => Loc::getMessage("LOAD_COMPANY_BY_INN_ACTIVITY_DESCR_FIELD_TEXT"),
            "TYPE" => "string",
        ],
    ],
];
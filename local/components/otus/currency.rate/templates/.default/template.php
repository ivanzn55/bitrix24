<?php
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


if($arResult['CURRENCY']){?>
    <div class="currency_rate">
        <?=Loc::getMessage('OTUS_CURRENCY_CAPTION')?> <?=$arResult['CURRENCY']['CURRENCY']?> - <?=$arResult['CURRENCY']['AMOUNT']?>
    </div>
<?php }
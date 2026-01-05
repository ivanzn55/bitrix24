<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
?>


<div class="doctors-list">

    <h1 class="doctors-list__title">
        <a href="/doctors">Врачи</a>
    </h1>

    <div class="doctors-list__actions">
        <a class="doctors-list__action" href="/doctors/?action=add">Добавить врача</a>
        <a class="doctors-list__action" href="/doctors/?action=add_proc">Добавить процедуру</a>
    </div>

    <div class="doctors-list__items">

        <?foreach($arResult['ITEMS'] as $arItem){?>
        <div class="doctors-list__item">
            <a class="doctors-list__item-wrap" href="/doctors/?ID=<?=$arItem['ID']?>&action=detail" >
                <?=$arItem['LAST_NAME']?>
                <?=$arItem['NAME']?>
                <?=$arItem['MIDDLE_NAME']?>
            </a>
        </div>
        <?}?>

    </div>

</div>
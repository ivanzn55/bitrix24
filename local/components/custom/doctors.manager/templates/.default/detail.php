<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
?>

<div class="doctor-detail">

    <div class="doctor-detail__actions">
        <a href="/doctors/" class="doctor-detail__action">← Назад к списку</a>
        <a href="/doctors/?ID=<?=$arResult['ID']?>&action=edit" class="doctor-detail__action">Редактировать</a>
    </div>

    <h1 class="doctor-detail__title"><?=$arResult['LAST_NAME'] . " " . $arResult['NAME'] . " " . $arResult['MIDDLE_NAME']?></h1>

    <?php if($arResult['PROCS']){?>

        <ul class="doctor-detail__procedures">
            <?php foreach($arResult['PROCS']as $proc):?>
                <li class="doctor-detail__procedures-item"><?=$proc['NAME']?></li>
            <?php endforeach?>
        </ul>

    <?php }?>
</div>
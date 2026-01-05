<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
?>

<form action="" method="POST" class="doctor-edit-form">
    <div class="doctor-edit-form__actions">
        <a href="/doctors/" class="doctor-detail__action">← Назад к списку</a>
    </div>
    <h2 class="doctor-edit-form__title"> Данные врача</h2>
    <div class="doctor-edit-form__content">
        <?=bitrix_sessid_post()?>

         <input type="hidden" name="ID" value="<?=$arResult['ID']?>">

        <input class="doctor-edit-form__input-text" type="text" name="NAME" placeholder="Имя врача" value="<?=$arResult['NAME']??''?>">
        <input class="doctor-edit-form__input-text" type="text" name="MIDDLE_NAME" placeholder="Отчество врача" value="<?=$arResult['MIDDLE_NAME']??''?>">
        <input class="doctor-edit-form__input-text" type="text" name="LAST_NAME" placeholder="Фамилия врача" value="<?=$arResult['LAST_NAME']??''?>">

        <select class="doctor-edit-form__select" multiple name="PROC_IDS[]">
            <option value="" selected disabled>Процедуры</option>

            <?php foreach($arResult['ALL_PROCEDURES'] as $proc):?>

                <option value="<?=$proc['ID']?>"
                    <?php if(isset($arResult['PROC_IDS']) && in_array($proc['ID'], $arResult['PROC_IDS'])):?> selected<?php endif?>
                >
                    <?=$proc['NAME']?>
                </option>

            <?php endforeach?>

        </select>

        <input class="doctor-edit-form__submit" type="submit" name="doctor-submit" value="Сохранить">
    </div>
</form>

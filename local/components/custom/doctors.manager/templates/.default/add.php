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
    <h2 class="doctor-edit-form__title"> Добавление врача</h2>
    <div class="doctor-edit-form__content">
        <?=bitrix_sessid_post()?>


        <input class="doctor-edit-form__input-text" type="text" name="NAME" placeholder="Имя врача" value="">
        <input class="doctor-edit-form__input-text" type="text" name="MIDDLE_NAME" placeholder="Отчество врача" value="">
        <input class="doctor-edit-form__input-text" type="text" name="LAST_NAME" placeholder="Фамилия врача" value="">

        <select class="doctor-edit-form__select" multiple name="PROC_IDS[]">
            <option value="" selected disabled>Процедуры</option>

            <?php foreach($arResult['ALL_PROCEDURES'] as $proc):?>

                <option value="<?=$proc['ID']?>">
                    <?=$proc['NAME']?>
                </option>

            <?php endforeach?>

        </select>

        <input class="doctor-edit-form__submit" type="submit" name="doctor-submit" value="Сохранить">
    </div>
</form>

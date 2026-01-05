<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
?>

<form class="doctor-edit-form" action="" method="POST">
    <div class="doctor-edit-form__actions">
        <a href="/doctors/" class="doctor-detail__action">← Назад к списку</a>
    </div>
    <h2 class="doctor-edit-form__title">Добавить процедуру</h2>
    <div class="doctor-edit-form__content">
        <?=bitrix_sessid_post()?>
        <input class="doctor-edit-form__input-text" type="text" name="NAME" placeholder="Название процедура">
        <input class="doctor-edit-form__submit" type="submit" name="proc-submit" placeholder="Сохранить">
    </div>
</form>
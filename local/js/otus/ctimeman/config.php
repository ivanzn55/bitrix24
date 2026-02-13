<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


return [
    'js' => './src/script.js',
    'css' => './src/style.css',
    'rel' => [
        'main.core',        // Требуется для BX.create, BX.PreventDefault, BX.message
        'main.popup',       // Требуется для BX.PopupWindow
        'ui.buttons',       // Для стилей кнопок ui-btn
    ],
    'lang' => './lang/ru/script.php', // Файл локализации
    'skip_core' => false, // Не пропускать ядро Bitrix
];
<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    'PARAMETERS' => array(

        'DETAIL_URL' => array(
            'PARENT' => 'URL_TEMPLATES',
            'NAME' => 'URL детальной страницы',
            'TYPE' => 'STRING',
            'DEFAULT' => 'index.php?ID=#ELEMENT_ID#'
        ),
        'EDIT_URL' => array(
            'PARENT' => 'URL_TEMPLATES',
            'NAME' => 'URL редактирования',
            'TYPE' => 'STRING',
            'DEFAULT' => 'index.php?ID=#ELEMENT_ID#&action=edit'
        ),
        'ADD_URL' => array(
            'PARENT' => 'URL_TEMPLATES',
            'NAME' => 'URL добавления',
            'TYPE' => 'STRING',
            'DEFAULT' => 'index.php?action=add'
        ),
        'LIST_URL' => array(
            'PARENT' => 'URL_TEMPLATES',
            'NAME' => 'URL списка',
            'TYPE' => 'STRING',
            'DEFAULT' => 'index.php'
        ),
        'ADD_PROC' => array(
            'PARENT' => 'URL_TEMPLATES',
            'NAME' => 'Добавление процедуры',
            'TYPE' => 'STRING',
            'DEFAULT' => 'index.php?action=proc_add'
        ),
        'SET_TITLE' => array(
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'Устанавливать заголовок страницы',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ),
    )
);
?>
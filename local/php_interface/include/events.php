<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandlerCompatible('main', 'OnPageStart', '\Otus\Event\Main::handlerPageStart');

$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserType\OnlineRecord',
        'GetUserTypeDescription'
    ]
);
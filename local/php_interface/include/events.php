<?php

use Otus\Event\DealHandler;
use Otus\Event\IblockElementEvent;
use Otus\Event\IblockElementHandler;

$eventManager = \Bitrix\Main\EventManager::getInstance();

//$eventManager->addEventHandlerCompatible('main', 'OnPageStart', '\Otus\Event\Main::handlerPageStart');

$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'Otus\UserType\OnlineRecord',
        'GetUserTypeDescription'
    ]
);


$eventManager->addEventHandler(
    "iblock",
    "OnAfterIBlockElementUpdate",
    [
        IblockElementHandler::class,
        'onElementAfterUpdate'
    ]
);

$eventManager->addEventHandlerCompatible(
    "crm",
    "OnAfterCrmDealUpdate",
    [
        DealHandler::class,
        'onAfterDealUpdate'
    ]
);

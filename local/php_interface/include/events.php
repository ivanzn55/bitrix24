<?php

use Otus\Event\DealHandler;
use Otus\Event\IblockElementEvent;
use Otus\Event\IblockElementHandler;
use Otus\Rest\TaskApi;

use Otus\Event\Main;

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

$eventManager->addEventHandlerCompatible(
    'rest',
    'OnRestServiceBuildDescription',
    [
        TaskApi::class,
        'OnRestServiceBuildDescriptionHandler'
    ]
);


$eventManager->addEventHandler(
    'main',
    'OnProlog',
    [Main::class, 'OnProlog']
);
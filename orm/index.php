<?php

use Otus\Orm\TaskTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Catalog\PriceTable;
use Otus\Models\ClientPropertyValuesTable;
use Otus\Models\CatalogPropertyValuesTable;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->SetTitle('Выборка ORM');

\Bitrix\Main\Loader::IncludeModule("iblock");

$tasks = TaskTable::query()
    ->setSelect([
        'ID' ,
        'NAME',
        'MANAGER',
        'CREATE_DATE',
        'COMMENT',
        'COMPLETED',
        'COMPANY_NAME' => 'CLIENT.NAME',
        'PHONE' => 'CLIENT.PHONE',
        'PERSON' => 'CLIENT.PERSON',
        'TARGET' => 'PRODUCT.NAME',
        'TARGET_ID' => 'PRODUCT.ID',
        'PRICE' => 'CATALOG_PRICE.PRICE',
        'CURRENCY' => 'CATALOG_PRICE.CURRENCY'
    ])
    ->where("COMPLETED", 'Y')
    ->addOrder('CREATE_DATE', 'DESC')
    ->registerRuntimeField(
        'CATALOG_PRICE',
        new ReferenceField(
            'CATALOG_PRICE',
            PriceTable::getEntity(),
            [
                '=this.TARGET_ID' => 'ref.PRODUCT_ID',
                '=ref.CATALOG_GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?', 1)
            ],
            ['join_type' => 'LEFT']
        )
    )
    ->setCacheTtl(60)
    ->cacheJoins(true)
    ->fetchAll();

echo "<pre>"; var_dump($tasks); echo "</pre>";
echo "<hr>";

$tasks = TaskTable::query()
    ->setSelect([
        'ID' ,
        'NAME',
        'MANAGER',
        'CREATE_DATE',
        'COMMENT',
        'COMPLETED',
        'COMPANY_NAME' => 'CLIENT.NAME',
        'PHONE' => 'CLIENT.PHONE',
        'PERSON' => 'CLIENT.PERSON',
        'TARGET' => 'PRODUCT.NAME'
    ])
    ->whereNot("COMPLETED", 'Y')
    ->addOrder('CREATE_DATE', 'DESC')
    ->setCacheTtl(60)
    ->cacheJoins(true)
    ->fetchAll();

echo "<pre>"; var_dump($tasks); echo "</pre>"; echo "<hr>";


$clients = ClientPropertyValuesTable::query()
    ->setSelect([
        'ID' => 'ELEMENT.ID' ,
        'NAME' => 'ELEMENT.NAME',
        'PHONE',
        'PERSON',
        'TASK_NAME' => 'TASK.NAME',
        'TASK_COMMENT' => 'TASK.COMMENT'
    ])
    ->where('ID', 1)
    ->setCacheTtl(60)
    ->cacheJoins(true)
    ->fetchAll();


echo "<pre>"; var_dump($clients); echo "</pre>"; echo "<hr>";

$products = CatalogPropertyValuesTable::query()
    ->setSelect([
        'ID' => 'ELEMENT.ID' ,
        'NAME' => 'ELEMENT.NAME',
        'COLOR',
        'TASK_NAME' => 'TASK.NAME',
        'TASK_COMMENT' => 'TASK.COMMENT'
    ])
    ->setCacheTtl(60)
    ->cacheJoins(true)
    ->fetchAll();

echo "<pre>"; var_dump($products); echo "</pre>"; echo "<hr>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
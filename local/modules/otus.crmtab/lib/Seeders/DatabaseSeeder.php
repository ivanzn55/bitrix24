<?php

namespace Otus\Crmtab\Seeders;

use Faker\Factory;
use Otus\Crmtab\Models\CatalogPropertyValuesTable;
use Otus\Crmtab\Models\ClientPropertyValuesTable;
use Otus\Crmtab\Orm\TaskTable;
use Bitrix\Main\Type\Date;

class DatabaseSeeder
{
    public static function addTasks(): void
    {
        $productsIds = [];
        $clientsIds = [];
        $products = CatalogPropertyValuesTable::query()
            ->setSelect([
                'ID' => 'ELEMENT.ID' ,
            ])
            ->setLimit(20)
            ->fetchAll();

        if($products){
            $productsIds = array_column($products, 'ID');
        }

        $clients = ClientPropertyValuesTable::query()
            ->setSelect([
                'ID' => 'ELEMENT.ID'
            ])
            ->setLimit(20)
            ->fetchAll();

        if($clients){
            $clientsIds = array_column($clients, 'ID');
        }

        $faker = Factory::create();

        for($i = 0; $i < 20; $i++){
            $tasks[] =  [
                'NAME' => $faker->word(),
                'MANAGER' => $faker->name(),
                'CREATE_DATE' => new Date($faker->dateTimeBetween('-2 years', 'now')->format('d.m.Y')),
                'COMMENT' => $faker->text(),
                'CLIENT_ID' => $clientsIds ? $faker->randomElement($clientsIds): false,
                'PRODUCT_ID' => $productsIds ? $faker->randomElement($productsIds) : false
            ];
        }

        TaskTable::addMulti($tasks, true);


    }
}
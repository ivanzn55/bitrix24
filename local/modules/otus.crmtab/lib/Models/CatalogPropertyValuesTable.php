<?php

namespace Otus\Crmtab\Models;

use Bitrix\Main\Entity\ReferenceField;
use Otus\Models\AbstractIblockPropertyValuesTable;
use Otus\Orm\TaskTable;

class CatalogPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    public const IBLOCK_ID = 14;


    public static function getMap(): array
    {


        $map = [
            'TASK' => new ReferenceField(
                'TASK',
                TaskTable::class,
                ['=this.ID' => 'ref.PRODUCT_ID']
            )
        ];


        return array_merge(parent::getMap(), $map);
    }
}
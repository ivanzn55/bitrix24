<?php

namespace Otus\Crmtab\Models;

use Bitrix\Main\Entity\ReferenceField;
use Otus\Models\AbstractIblockPropertyValuesTable;
use Otus\Orm\TaskTable;

class ClientPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    public const IBLOCK_ID = 5;


    public static function getMap(): array
    {


        $map = [
            'TASK' => new ReferenceField(
                'TASK',
                TaskTable::class,
                ['=this.ID' => 'ref.CLIENT_ID']
            )
        ];


        return array_merge(parent::getMap(), $map);
    }
}
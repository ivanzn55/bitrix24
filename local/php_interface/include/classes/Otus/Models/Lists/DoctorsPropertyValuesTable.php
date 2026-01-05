<?php

namespace Otus\Models\Lists;

use Bitrix\Main\Entity\ReferenceField;
use Otus\Models\AbstractIblockPropertyValuesTable;

class DoctorsPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    public const IBLOCK_ID = 16;


    public static function getMap(): array
    {


        $map = [
            'PROCEDURES' => new ReferenceField(
                'PROCEDURES',
                ProcsPropertyValuesTable::class,
                ['=this.PROC_IDS' => 'ref.IBLOCK_ELEMENT_ID']
            )
        ];


        return array_merge(parent::getMap(), $map);
    }

}
<?php

namespace Otus\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

use Bitrix\Iblock\Elements\ElementClientTable;
use Bitrix\Iblock\Elements\ElementCatalogTable;


class TaskTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'otus_task';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new StringField('NAME'))
                ->configureRequired()
                ->configureSize(100),

            (new StringField('MANAGER'))
                ->configureRequired()
                ->configureSize(100),

            (new DateField('CREATE_DATE')),

            (new TextField('COMMENT')),

            (new BooleanField('COMPLETED'))
                ->configureValues('N', 'Y')
                ->configureDefaultValue('N'),

            (new IntegerField('CLIENT_ID')),
            (new IntegerField('PRODUCT_ID')),

            (new Reference('CLIENT', ElementClientTable::class, Join::on('this.CLIENT_ID', 'ref.ID')))
                ->configureJoinType('INNER'),

            (new Reference('PRODUCT', ElementCatalogTable::class, Join::on('this.PRODUCT_ID', 'ref.ID')))
                ->configureJoinType('INNER'),

        ];
    }
}
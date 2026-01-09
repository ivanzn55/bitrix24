<?php

use Bitrix\Catalog\PriceTable;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Result;
use Otus\Crmtab\Orm\TaskTable;
use Bitrix\Main\Entity\ReferenceField;

Loader::includeModule('otus.crmtab');
Loader::IncludeModule("iblock");
Loader::IncludeModule("catalog");
class BookGrid extends \CBitrixComponent implements Controllerable
{
    public function configureActions(): array
    {
        return [];
    }

    private function getElementActions(): array
    {
        return [];
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
            ],
            [
                'id' => 'NAME',
                'name' => Loc::getMessage('TAKS_GRID_TASK_TITLE_LABEL'),
                'sort' => 'NAME',
                'default' => true,
            ],
            [
                'id' => 'MANAGER',
                'name' => Loc::getMessage('TAKS_GRID_TASK_MANAGER'),
                'sort' => 'MANAGER',
                'default' => true,
            ],
            [
                'id' => 'CREATE_DATE',
                'name' => Loc::getMessage('TAKS_GRID_TASK_CREATE_DATE_LABEL'),
                'sort' => 'CREATE_DATE',
                'default' => true,
            ],
            [
                'id' => 'COMMENT',
                'name' => Loc::getMessage('TAKS_GRID_TASK_COMMENT_LABEL'),
                'default' => true,
            ],

            [
                'id' => 'COMPLETED',
                'name' => Loc::getMessage('TAKS_GRID_TASK_COMPLETED_LABEL'),
                'sort' => 'COMPLETED',
                'default' => true,
            ],

            [
                'id' => 'COMPANY_NAME',
                'name' => Loc::getMessage('TAKS_GRID_TASK_COMPANY_NAME_LABEL'),
                'sort' => 'COMPANY_NAME',
                'default' => true,
            ],

            [
                'id' => 'PERSON',
                'name' => Loc::getMessage('TAKS_GRID_TASK_PERSON_LABEL'),
                'sort' => 'PERSON',
                'default' => true,
            ],

            [
                'id' => 'PRODUCT_NAME',
                'name' => Loc::getMessage('TAKS_GRID_TASK_PRODUCT_NAME_LABEL'),
                'sort' => 'PRODUCT_NAME',
                'default' => true,
            ],

            [
                'id' => 'PRICE',
                'name' => Loc::getMessage('TAKS_GRID_TASK_PRICE_LABEL'),
                'sort' => 'PRICE',
                'default' => true,
            ],


        ];
    }

    public function executeComponent(): void
    {
        $this->prepareGridData();
        $this->includeComponentTemplate();
    }

    private function prepareGridData(): void
    {
        $this->arResult['HEADERS'] = $this->getHeaders();
        $this->arResult['FILTER_ID'] = 'TASK_GRID';

        $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_ID']);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilter($filterData);


        $sort = $gridOptions->getSorting([
            'sort' => [
                'ID' => 'DESC',
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order',
            ],
        ]);


        $countQuery = TaskTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
        ;
        $nav->setRecordCount($countQuery->queryCountTotal());



        $taskIdsQuery = TaskTable::query()
            ->setSelect(
                [
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
                ]
            )
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
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort'])
        ;


        $dbTask = $taskIdsQuery->exec();



        if ($dbTask->getSelectedRowsCount() > 0) {
            $this->arResult['GRID_LIST'] = $this->prepareGridList($dbTask);
        } else {
            $this->arResult['GRID_LIST'] = [];
        }

        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%NAME'] = $filterData['FIND'];
        }

        if (!empty($filterData['NAME'])) {
            $filter['%NAME'] = $filterData['NAME'];
        }


        if (!empty($filterData['PUBLISH_DATE_from'])) {
            $filter['>=CREATE_DATE'] = $filterData['CREATE_DATE_from'];
        }

        if (!empty($filterData['PUBLISH_DATE_to'])) {
            $filter['<=CREATE_DATE'] = $filterData['CREATE_DATE_to'];
        }

        return $filter;
    }

    private function prepareGridList(Result $tasks): array
    {
        $gridList = [];


        while ($task = $tasks->fetch()) {

            $gridList[] = [
                'data' => [
                    'ID' => $task['ID'],
                    'NAME' => $task['NAME'],
                    'MANAGER' => $task['MANAGER'],
                    'CREATE_DATE' => $task['CREATE_DATE']->format('d.m.Y'),
                    'COMMENT' => $task['COMMENT'],
                    'COMPLETED' => $task['COMPLETED'],
                    'COMPANY_NAME' => $task['COMPANY_NAME'],
                    'PERSON' => $task['PERSONVALUE'],
                    'PRODUCT_NAME' => $task['PRODUCT_NAME'],
                    'PRICE' => $task['PRICE']
                ],
                'actions' => $this->getElementActions(),
            ];

        }



        return $gridList;
    }

    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'NAME',
                'name' => Loc::getMessage('TAKS_GRID_TASK_TITLE_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'CREATE_DATE',
                'name' => Loc::getMessage('TAKS_GRID_TASK_CREATE_DATE_LABEL'),
                'type' => 'date',
                'default' => true,
            ],
        ];
    }
}
<?php

namespace Otus\Event;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;
use Bitrix\Iblock\Elements;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class IblockElementHandler
{
    const IBLOCK_APPLICATIONS_CODE = 'applications';

    /**
     * Возвращает ID инфоблока заявки
     *
     * @return int
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getApplicationsBlockId(): int
    {

        Loader::IncludeModule("iblock");

        $iblockApp = IblockTable::query()
            ->addSelect('ID')
            ->where('CODE', self::IBLOCK_APPLICATIONS_CODE)
            ->setLimit(1)
            ->setCacheTtl(3600)
            ->fetch();

        return $iblockApp['ID'] ?? 0;
    }

    /**
     * Обработчик после обновления элемента инфоблока
     *
     * @param $arFields
     * @return void
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function onElementAfterUpdate(&$arFields)
    {


        $iblockApp = self::getApplicationsBlockId();

        if($iblockApp > 0 && $iblockApp == $arFields['IBLOCK_ID']){

            if (!Loader::includeModule('crm')) {
                return;
            }

            $dealFactory =
                Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

            $item = Elements\ElementApplicationsTable::query()
                ->setSelect(['DEAL' => 'DEAL', 'RESPONSIBLE' => 'RESPONSIBLE', 'SUM' => 'SUM'])
                ->where('ID', $arFields['ID'])
                ->fetch();

            $sum = 0;
            $currency = '';

            if (str_contains($item['SUMVALUE'], '|')) {
                list($sum, $currency) = explode('|', $item['SUMVALUE']);
            }

            $existedDealId = $item['DEALVALUE'];
            $dealItem = $dealFactory->getItem($existedDealId);

            if($dealItem){

                $update = false;

                $currentSum = (float)$dealItem->get('OPPORTUNITY');
                $newSum = (float)$sum;

                if($currentSum !== $newSum){
                    $dealItem->set('OPPORTUNITY', $sum);
                    $update = true;
                }

                $currentCurrency = $dealItem->get('CURRENCY_ID');
                if($currentCurrency !== $currency){
                    $dealItem->set('CURRENCY_ID', $currency);
                    $update = true;
                }

                $currentResponsible = (int)$dealItem->get('ASSIGNED_BY_ID');
                $newResponsible = (int)$item['RESPONSIBLEIBLOCK_GENERIC_VALUE'];

                if ($currentResponsible !== $newResponsible) {
                    $dealItem->set('ASSIGNED_BY_ID', $item['RESPONSIBLEIBLOCK_GENERIC_VALUE']);
                    $update = true;
                }

                if($update){
                    $dealUpdateOperation = $dealFactory->getUpdateOperation($dealItem);
                    $updateResult = $dealUpdateOperation->launch();
                }

            }


        }
    }

}
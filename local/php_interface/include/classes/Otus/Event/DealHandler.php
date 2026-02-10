<?php

namespace Otus\Event;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;
use Bitrix\Iblock\Elements;
use Bitrix\Main\LoaderException;

class DealHandler
{
    /**
     * Обработчик после обновления сделки
     *
     * @param $arFields
     * @return void
     * @throws ArgumentException
     * @throws LoaderException
     */
    public static function onAfterDealUpdate(&$arFields)
    {
        Loader::IncludeModule("iblock");


        $item = Elements\ElementApplicationsTable::query()
            ->setSelect(['ID', 'DEAL' => 'DEAL', 'RESPONSIBLE' => 'RESPONSIBLE', 'SUM' => 'SUM'])
            ->where('DEAL.VALUE', $arFields['ID'])
            ->fetch();


        if($item){

            $sum = 0;
            $currency = '';

            if (str_contains($item['SUMVALUE'], '|')) {
                list($sum, $currency) = explode('|', $item['SUMVALUE']);
            }

            // $arFields['OPPORTUNITY']
            // $arFields['CURRENCY_ID']
            // $arFields['ASSIGNED_BY_ID']


            $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
            $dealItem = $dealFactory->getItem($arFields['ID']);
            $currentCurrency = $dealItem->get('CURRENCY_ID');
            $currentSum = (float)$dealItem->get('OPPORTUNITY');
            $currentResponsible = $dealItem->get('ASSIGNED_BY_ID');

            $properties = [];

            if((float)$sum != $currentSum || $currency != $currentCurrency){
                $properties['SUM'] = $currentSum.'|'.$currency;
            }

            if($currentResponsible != $item['RESPONSIBLEIBLOCK_GENERIC_VALUE']){
                $properties['ASSIGNED_BY_ID'] = $currentResponsible;
            }

            if($properties){

                \CIBlockElement::SetPropertyValuesEx(
                    $item['ID'],
                    false,
                    $properties
                );

            }

        }

    }

}
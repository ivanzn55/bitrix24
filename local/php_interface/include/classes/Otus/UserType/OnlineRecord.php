<?php

namespace Otus\UserType;

use Bitrix\Main\LoaderException;
use Bitrix\Main\Page\Asset;
use CJSCore;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Main\UI\Extension;


class OnlineRecord
{
    /**
     * Метод возвращает массив собственного типа свойств
     * @return array
     */
    public static function getUserTypeDescription(): array
    {
        return [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'ONLINE_RECORD',
            'DESCRIPTION' => 'Онлайн-запись',

            'GetPropertyFieldHtml' => [self::class, 'GetPropertyFieldHtml'],    // отображение свойства
            'GetPublicViewHTML' => [self::class, 'GetPublicViewHTML'],          // вид публичная часть
            'GetPublicEditHTML' => [self::class, 'GetPropertyFieldHtml'],       // вид при редактировании
        ];
    }

    /**
     * @return string
     */
    public static function GetPropertyFieldHtml(): string
    {
        return '<button id="online-record-btn" type="button" class="online-record-btn">Записаться заглушка</button>';
    }

    /**
     *  Метод возвращает массив доступных процедур врача
     * @param $id
     * @return array
     * @throws LoaderException
     */
    public static function getDataValues($id): array
    {
        if(empty($id)){
            return [];
        }

        Loader::includeModule("iblock");

        $data = ElementDoctorsTable::query()
            ->setSelect(['PROC_IDS.ELEMENT'])
            ->where('ID', $id)
            ->exec()
            ->fetchObject();

        $values = [];

        $procedures = $data->getProcIds()?->getAll();

        if($procedures){

            foreach($procedures as $procedure){
                $values[$procedure->getElement()->getId()] = $procedure->getElement()->getName();
            }
        }

        return $values;
    }


    /**
     * Публичный вывод свойства
     * @param $arProperty
     * @param $arValue
     * @param $strHTMLControlName
     * @return string
     * @throws LoaderException
     */

    public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName): string
    {
        // Пытаемся определить ID элемента, если его нет в стандартном ключе
        $doctorId = $arProperty['ELEMENT_ID'] ?? $arValue['ELEMENT_ID'] ?? null;

        if (!$doctorId) {
            return '';
        }

        $procedures = self::getDataValues($doctorId);
        if (empty($procedures)) {
            return 'У врача нет доступных процедур';
        }
        
        Extension::load('otus.online_record');

        return self::renderLinks($doctorId, $procedures);
    }


    /**
     * Формирование HTML-ссылок
     * @param $doctorId
     * @param $procedures
     * @return string
     */
    private static function renderLinks($doctorId, $procedures): string
    {
        $html = '<div class="online-record-wrapper">';
        foreach ($procedures as $id => $name) {
            $html .= sprintf(
                '<a href="javascript:void(0);" 
                    class="book_procedure" 
                    data-pr-id="%s" 
                    data-doctor-id="%s" 
                    style="display:block; margin-bottom:5px;">%s</a>',
                htmlspecialcharsbx($id),
                htmlspecialcharsbx($doctorId),
                htmlspecialcharsbx($name)
            );
        }
        $html .= '</div>';
        return $html;
    }

}
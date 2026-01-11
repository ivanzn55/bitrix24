<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Otus\Models\Lists\RecordOnlinePropertyValueTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;



class OnlineRecordControllerComponent extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'addRecord' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
                    new ActionFilter\Csrf(),
                ],
            ],
        ];
    }

    public function addRecordAction()
    {

        Loader::IncludeModule("iblock");

        $dateTime = new DateTime();
        $fields['NAME'] = $this->request->getPost('NAME').' '.$dateTime->toString();

        $date = $this->request->getPost('TIME');
        $fields['DATE'] = $date ? new DateTime($date, 'Y-m-d\TH:i') : false;
        $fields['PATIENT_NAME'] = $this->request->getPost('NAME');
        $fields['PROC_IDS'] = $this->request->getPost('PROC_ID');
        $fields['DOCTOR'] = $this->request->getPost('DOCTOR_ID');


        if (RecordOnlinePropertyValueTable::add($fields)) {
            return ['status' => 'error', 'message' => 'Ошибка при удалении записи '];
        }

        return ['status' => 'success'];
    }

}
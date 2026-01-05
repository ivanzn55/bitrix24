<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Otus\Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;
use Otus\Models\Lists\ProcsPropertyValuesTable as ProcsTable;
use Bitrix\Main\Loader;

class DoctorsManagerComponent extends CBitrixComponent
{
    private $errors = array();
    private $iblockFields = array();
    private $properties = array();


    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->determineAction();
            $this->includeComponentTemplate($this->arResult['TEMPLATE_PAGE']);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->arResult['ERRORS'] = $this->errors;
            //$this->includeComponentTemplate('error');
        }
    }

    private function checkModules()
    {
        if (!Loader::IncludeModule("iblock")) {
            throw new Exception('Модуль инфоблоков не установлен');
        }
    }

    private function determineAction()
    {
        $action = $this->request->get('action');
        $elementId = $this->request->get('ID');

        switch ($action) {
            case 'add':
                $this->processAdd();
                $templatePage = 'add';
                break;
            case 'add_proc':
                $this->processAddProc();
                $templatePage = 'proc_add';
                break;
            case 'edit':
                $this->processEdit($elementId);
                $templatePage = 'edit';
                break;
            case 'detail':
                $this->processDetail($elementId);
                $templatePage = 'detail';
                break;
            default:
                $this->processList();
                $templatePage = 'list';
                break;
        }

        $this->arResult['TEMPLATE_PAGE'] = $templatePage;
    }

    private function processList()
    {

        $doctors = DoctorsTable::query()
            ->setSelect([
                'NAME' => 'ELEMENT.NAME',
                'LAST_NAME',
                'MIDDLE_NAME',
                'ID' => 'ELEMENT.ID'

            ])
            ->fetchCollection();

        $this->arResult['ITEMS'] = [];

        foreach ($doctors as $doctor) {

            $this->arResult['ITEMS'][] = [
                'ID' => $doctor->getElement()->getId(),
                'NAME' => $doctor->getElement()->getName(),
                'MIDDLE_NAME' => $doctor->getMiddleName(),
                'LAST_NAME' => $doctor->getLastName(),
            ];

        }


        if ($this->arParams['SET_TITLE'] === 'Y') {
            $GLOBALS['APPLICATION']->SetTitle('Список врачей');
        }
    }

    protected function getDoctorById(int $elementId) :  array
    {
        $doctor = DoctorsTable::query()
            ->setSelect([
                '*',
                'NAME' => 'ELEMENT.NAME',
                'PROC_IDS',
                'ID' => 'ELEMENT.ID'
            ])->where("ID", $elementId)
            ->fetch();

        if (!$doctor) {
            throw new Exception('Элемент не найден');
        }

        if($doctor['PROC_IDS']){
            $doctor["PROCS"] = ProcsTable::query()
                ->setSelect(['NAME' => 'ELEMENT.NAME', 'ID' => 'ELEMENT.ID'])
                ->where("ELEMENT.ID", 'in', $doctor['PROC_IDS'])
                ->fetchAll();
        }

        return $doctor ?: [];
    }

    protected function getAllProcedures() :  array
    {
        $procs = ProcsTable::query()
            ->setSelect(['NAME' => 'ELEMENT.NAME', 'ID' => 'ELEMENT.ID'])
            ->fetchAll();

        return $procs ?: [];
    }



    private function processDetail($elementId)
    {
        if (!$elementId) {
            throw new Exception('Не указан ID элемента');
        }

        $this->arResult = $this->getDoctorById($elementId);

        if ($this->arParams['SET_TITLE'] === 'Y') {
            $GLOBALS['APPLICATION']->SetTitle($this->arResult['NAME']);
        }
    }

    private function processAdd()
    {

        if ($this->request->isPost() && check_bitrix_sessid()) {
            //$this->saveElement();

            $fields['NAME'] = $this->request->getPost('NAME');
            $fields['MIDDLE_NAME'] = $this->request->getPost('MIDDLE_NAME');
            $fields['LAST_NAME'] = $this->request->getPost('LAST_NAME');
            $fields['PROC_IDS'] = $this->request->getPost('PROC_IDS');

            if( DoctorsTable::add($fields)){
                header('Location: /doctors');
                exit();
            }  else echo "Произошла ошибка";

        }

        $this->arResult['ALL_PROCEDURES'] = $this->getAllProcedures();

        if ($this->arParams['SET_TITLE'] === 'Y') {
            $GLOBALS['APPLICATION']->SetTitle('Добавление доктора');
        }
    }

    private function processAddProc()
    {

        if ($this->request->isPost() && check_bitrix_sessid()) {
            //$this->saveElement();

            $fields['NAME'] = $this->request->getPost('NAME');


            if( ProcsTable::add($fields)){
                header('Location: /doctors');
                exit();
            }  else echo "Произошла ошибка";

        }

        if ($this->arParams['SET_TITLE'] === 'Y') {
            $GLOBALS['APPLICATION']->SetTitle('Добавление процедуры');
        }
    }

    private function processEdit($elementId)
    {
        if (!$elementId) {
            throw new Exception('Не указан ID элемента');
        }

        $this->arResult = $this->getDoctorById($elementId);

        $this->arResult['ALL_PROCEDURES'] = $this->getAllProcedures();


        if ($this->request->isPost() && check_bitrix_sessid()) {

            //echo "<pre>"; var_dump($this->request->getPostList()); echo "</pre>"; die();

            \CIBlockElement::SetPropertyValues($elementId, DoctorsTable::IBLOCK_ID, $this->request->getPost('PROC_IDS'), "PROC_IDS");

            $fields['NAME'] = $this->request->getPost('NAME');
            $fields['MIDDLE_NAME'] = $this->request->getPost('MIDDLE_NAME');
            $fields['LAST_NAME'] = $this->request->getPost('LAST_NAME');


            if(DoctorsTable::update($elementId, $fields)){
                header('Location: /doctors');
                exit();
            }

        }

        if ($this->arParams['SET_TITLE'] === 'Y') {
            $GLOBALS['APPLICATION']->SetTitle('Редактирование: ' . $this->arResult['NAME']);
        }
    }



}
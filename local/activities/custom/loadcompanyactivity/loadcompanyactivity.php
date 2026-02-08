<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Dadata\DadataClient;
use Bitrix\Crm\CompanyTable;
use Bitrix\Main\Type\DateTime;

class CBPLoadCompanyActivity extends BaseActivity
{
    /**
     * @see parent::_construct()
     * @param $name string Activity name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Inn' => '',
            'ResponsibleId' => '',

            // return
            'Text' => null,
        ];

        $this->SetPropertiesTypes([
            'Inn' => ['Type' => FieldType::STRING],
            'ResponsibleId' => ['Type' => FieldType::INT],
            'Text' => ['Type' => FieldType::STRING],
        ]);
    }

    /**
     * @return ErrorCollection
     */
    protected function internalExecute(): ErrorCollection
    {
        $errors = parent::internalExecute();

        $this->log('Запуск');

        try {
            // Получаем значения свойств активности
            $inn = $this->Inn;
            $responsibleId = $this->ResponsibleId;

            //$this->log('значение поля UF_COMPANY_INN:'.' '.$inn);

            if (empty($inn)) {
                $errors->setError(new Error(Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_ERROR_EMPTY_INN')));
                return $errors;
            }

            if (empty($responsibleId)) {
                $responsibleId = 1; // Значение по умолчанию
            }

            // Получаем переменные бизнес-процесса
            $rootActivity = $this->GetRootActivity();
            $token = $rootActivity->GetVariable("TOKEN");
            $secret = $rootActivity->GetVariable("SECRET");
            $elementId = $rootActivity->GetVariable("ELEMENT_ID");

            if (empty($token) || empty($secret)) {
                $errors->setError(new Error(Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_ERROR_NO_CREDENTIALS')));
                return $errors;
            }

            // Ищем или создаем компанию
            $companyId = $this->findOrCreateCompany($inn, $token, $secret, $responsibleId, $errors);

            if ($companyId && $errors->isEmpty()) {
                // Устанавливаем название компании в возвращаемое свойство
                $this->Text = $this->getCompanyNameById($companyId);

                // Если есть элемент инфоблока, обновляем его свойство
                if ($elementId) {
                    $this->updateElementProperty($elementId, $companyId);
                }
            }

        } catch (\Exception $e) {
            $errors->setError(new Error($e->getMessage()));
        }

        return $errors;
    }


    /**
     *  Получение названия компании по ID
     *
     * @param int $companyId
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    private function getCompanyNameById(int $companyId): string
    {
        if (!Loader::includeModule('crm')) {
            return '';
        }

        $company = CompanyTable::getList([
            'select' => ['TITLE'],
            'filter' => ['ID' => $companyId],
            'limit' => 1
        ])->fetch();

        return $company ? $company['TITLE'] : '';
    }


    /**
     * Обновление свойства элемента
     *
     * @param int $elementId
     * @param int $companyId
     * @return void
     * @throws \Bitrix\Main\LoaderException
     */
    private function updateElementProperty(int $elementId, int $companyId): void
    {
        if (Loader::includeModule('iblock')) {
            CIBlockElement::SetPropertyValuesEx(
                $elementId,
                false,
                ['COMPANY' => $companyId]
            );
        }
    }


    /**
     * Поиск или создание компании
     *
     * @param string $inn
     * @param string $token
     * @param string $secret
     * @param int $responsibleId
     * @param ErrorCollection $errors
     * @return int|null
     * @throws \Bitrix\Main\LoaderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function findOrCreateCompany(string $inn, string $token, string $secret, int $responsibleId, ErrorCollection $errors): ?int
    {

        //UF_COMPANY_INN
        // Сначала проверяем, есть ли уже компания с таким ИНН
        $existingCompanyId = $this->findCompanyByInn($inn);
        if ($existingCompanyId) {
            return $existingCompanyId;
        }

        // Если нет, ищем в DaData
        try {
            $dadata = new DadataClient($token, $secret);
            $response = $dadata->findById("party", $inn);

            if (empty($response)) {
                $errors->setError(new Error(Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_ERROR_COMPANY_NOT_FOUND', ['#INN#' => $inn])));
                return null;
            }

            $companyName = $response[0]['value'];
            $data = $response[0]['data'];

            // Создаем новую компанию
            $companyId = $this->createCompany($companyName, $data, $responsibleId);

            if (!$companyId) {
                $errors->setError(new Error(Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_ERROR_CREATE_FAILED')));
                return null;
            }

            $GLOBALS['USER_FIELD_MANAGER']->Update('CRM_COMPANY', $companyId, [
                'UF_COMPANY_INN' => $inn
            ]);

            return $companyId;

        } catch (\Exception $e) {
            $errors->setError(new Error(Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_ERROR_DADATA_API', ['#ERROR#' => $e->getMessage()])));
            return null;
        }
    }


    /**
     * Поиск компании по инн
     *
     * @param string $inn
     * @return int|null
     * @throws \Bitrix\Main\LoaderException
     */
    private function findCompanyByInn(string $inn): ?int
    {
        if (!Loader::includeModule('crm')) {
            return null;
        }

        $company = CompanyTable::getList([
            'select' => ['ID'],
            'filter' => ['UF_COMPANY_INN' => $inn],
            'limit' => 1
        ])->fetch();

        return $company ? (int)$company['ID'] : null;
    }


    /**
     *  Создание компании
     *
     * @param string $companyName
     * @param array $data
     * @param int $responsibleId
     * @return int|null
     * @throws \Bitrix\Main\LoaderException
     */
    private function createCompany(string $companyName, array $data, int $responsibleId): ?int
    {
        if (!Loader::includeModule('crm')) {
            return null;
        }

        $arNewCompany = [
            "TITLE" => $companyName,
            "OPENED" => "Y",
            "COMPANY_TYPE" => "CUSTOMER",
            "ASSIGNED_BY_ID" => $responsibleId,
            "DATE_CREATE" => new DateTime(),
            "ADDRESS" => $data['address']['unrestricted_value'] ?? '',
        ];

        // Добавляем телефон
        if (!empty($data['phones'][0]['value'])) {
            $arNewCompany['PHONE'] = [[
                "VALUE" => $data['phones'][0]['value'],
                "VALUE_TYPE" => "WORK",
            ]];
        }

        // Добавляем email
        if (!empty($data['emails'][0]['value'])) {
            $arNewCompany['EMAIL'] = [[
                "VALUE" => $data['emails'][0]['value'],
                "VALUE_TYPE" => "WORK",
            ]];
        }

        // Добавляем адрес
        if (!empty($data['address']['unrestricted_value'])) {
            $arNewCompany['ADDRESS'] = $data['address']['unrestricted_value'];
        }

        $company = new \CCrmCompany(false);

        return $company->Add($arNewCompany);
    }


    /**
     * Return activity file path
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * @param PropertiesDialog|null $dialog
     * @return array[]
     */
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'Inn' => [
                'Name' => Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_FIELD_SUBJECT'),
                'FieldName' => 'inn',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Options' => [],
            ],
            'ResponsibleId' => [
                'Name' => Loc::getMessage('LOAD_COMPANY_BY_INN_ACTIVITY_FIELD_RESPONSIBLE_ID'),
                'FieldName' => 'ResponsibleId',
                'Type' => FieldType::INT,
                'Required' => true,
                'Options' => [],
            ],
        ];
        return $map;
    }


}
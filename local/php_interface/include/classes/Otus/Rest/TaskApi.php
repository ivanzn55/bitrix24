<?php

namespace Otus\Rest;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Rest\RestException;
use Bitrix\Main\Event;
use Bitrix\Main\Localization\Loc;
use Otus\Crmtab\Orm\TaskTable;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Diag\Debug;

Loc::loadMessages(__FILE__);

class TaskApi extends \IRestService
{
    /**
     * Register rest methods and events.
     * Clear scope cache after register:
     * \Bitrix\Main\Data\Cache::clearCache(true, '/rest/scope/');
     *
     * @return array
     */
    public static function OnRestServiceBuildDescriptionHandler(): array
    {
        return [
            'otus.task' => [
                'otus.task.add'    => [__CLASS__, 'add'],
                'otus.task.update' => [__CLASS__, 'update'],
                'otus.task.get'    => [__CLASS__, 'get'],
                'otus.task.delete' => [__CLASS__, 'delete'],
                'otus.task.list'   => [__CLASS__, 'list'],

                \CRestUtil::EVENTS => [

                    'onAfterOtusTaskAdd' => [
                        'otus.crmtab',
                        'onAfterOtusTaskAdd',
                        [__CLASS__, 'prepareEventData'],
                    ],
                    'onAfterOtusTaskUpdate' => [
                        'otus.crmtab',
                        'onAfterOtusTaskUpdate',
                        [__CLASS__, 'prepareEventData'],
                    ],
                    'onAfterOtusTaskDelete' => [
                        'otus.crmtab',
                        'onAfterOtusTaskDelete',
                        [__CLASS__, 'prepareEventData'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Добавление задачи.
     *
     * @param array       $query  Параметры запроса
     * @param int         $nav    Параметр пагинации
     * @param \CRestServer $server Объект REST-сервера
     * @return array
     * @throws RestException
     */
    public static function add($query, $nav, \CRestServer $server): array
    {
        Debug::writeToFile(array('PARAMS' => $query, 'ACTION' => 'ADD' ),"","logRestEvent.txt");
        Debug::writeToFile(array('NAV' => $nav ),"","logRestEvent.txt");

        $result = [];

        try {
            Loader::includeModule('otus.crmtab');

            // Валидация обязательных полей
            foreach (['NAME', 'MANAGER', 'CLIENT_ID', 'PRODUCT_ID'] as $field) {
                if (empty($query[$field])) {
                    throw new \Exception("Поле '{$field}' обязательно");
                }
            }

            // Подготовка данных
            $data = [
                'NAME'        => trim($query['NAME']),
                'MANAGER'     => trim($query['MANAGER']),
                'COMMENT'     => $query['COMMENT'] ?? '',
                'COMPLETED'   => ($query['COMPLETED'] ?? 'N') === 'Y' ? 'Y' : 'N',
                'CREATE_DATE' => new Date(),
            ];

            if (!empty($query['CLIENT_ID'])) {
                $data['CLIENT_ID'] = (int)$query['CLIENT_ID'];
            }

            if (!empty($query['PRODUCT_ID'])) {
                $data['PRODUCT_ID'] = (int)$query['PRODUCT_ID'];
            }

            $addResult = TaskTable::add($data);

            if ($addResult->isSuccess()) {
                $id = $addResult->getId();
                $data['ID'] = $id;

                $event = new Event('otus.crmtab', 'onAfterOtusTaskAdd', ['FIELDS' => $data]);
                $event->send();

                $result['id']      = $id;
                $result['success'] = true;
            } else {
                throw new \Exception(implode('; ', $addResult->getErrorMessages()));
            }

        } catch (\Exception $e) {
            throw new RestException(
                $e->getMessage(),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return $result;
    }

    /**
     * Обновление задачи по ID.
     *
     * @param array       $query
     * @param int         $nav
     * @param \CRestServer $server
     * @return array
     * @throws RestException
     */
    public static function update($query, $nav, \CRestServer $server): array
    {
        $result = [];

        Debug::writeToFile(array('PARAMS' => $query, 'ACTION' => 'UPDATE' ),"","logRestEvent.txt");
        Debug::writeToFile(array('NAV' => $nav ),"","logRestEvent.txt");

        try {
            Loader::includeModule('otus.crmtab');

            if (empty($query['ID'])) {
                throw new \Exception("Поле 'ID' обязательно");
            }

            $id = (int)$query['ID'];

            $task = TaskTable::getById($id)->fetch();
            if (!$task) {
                throw new \Exception("Задача с ID {$id} не найдена");
            }

            $data = [];

            if (isset($query['NAME']))       $data['NAME']       = trim($query['NAME']);
            if (isset($query['MANAGER']))    $data['MANAGER']    = trim($query['MANAGER']);
            if (isset($query['COMMENT']))    $data['COMMENT']    = $query['COMMENT'];
            if (isset($query['COMPLETED']))  $data['COMPLETED']  = $query['COMPLETED'] === 'Y' ? 'Y' : 'N';
            if (isset($query['CLIENT_ID']))  $data['CLIENT_ID']  = (int)$query['CLIENT_ID'];
            if (isset($query['PRODUCT_ID'])) $data['PRODUCT_ID'] = (int)$query['PRODUCT_ID'];

            $updateResult = TaskTable::update($id, $data);

            if ($updateResult->isSuccess()) {
                $data['ID'] = $id;

                $event = new Event('otus.crmtab', 'onAfterOtusTaskUpdate', ['FIELDS' => $data]);
                $event->send();

                $result['id']      = $id;
                $result['success'] = true;
            } else {
                throw new \Exception(implode('; ', $updateResult->getErrorMessages()));
            }

        } catch (\Exception $e) {
            throw new RestException(
                $e->getMessage(),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return $result;
    }

    /**
     * Получение задачи по ID.
     *
     * @param array       $query
     * @param int         $nav
     * @param \CRestServer $server
     * @return array
     * @throws RestException
     */
    public static function get($query, $nav, \CRestServer $server): array
    {
        $result = [];

        Debug::writeToFile(array('PARAMS' => $query, 'ACTION' => 'GET' ),"","logRestEvent.txt");
        Debug::writeToFile(array('NAV' => $nav ),"","logRestEvent.txt");

        try {
            Loader::includeModule('otus.crmtab');

            if (empty($query['ID'])) {
                throw new \Exception("Поле 'ID' обязательно");
            }

            $id = (int)$query['ID'];

            $task = TaskTable::getList([
                'select' => ['*', 'CLIENT_NAME' => 'CLIENT.NAME', 'PRODUCT_NAME' => 'PRODUCT.NAME'],
                'filter' => ['=ID' => $id],
                'limit'  => 1,
            ])->fetch();



            $result['task']    = $task ? self::formatTask($task) : null;
            $result['success'] = true;

        } catch (\Exception $e) {
            throw new RestException(
                $e->getMessage(),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return $result;
    }

    /**
     * Удаление задачи по ID.
     *
     * @param array       $query
     * @param int         $nav
     * @param \CRestServer $server
     * @return array
     * @throws RestException
     */
    public static function delete($query, $nav, \CRestServer $server): array
    {
        $result = [];

        Debug::writeToFile(array('PARAMS' => $query, 'ACTION' => 'DELETE' ),"","logRestEvent.txt");
        Debug::writeToFile(array('NAV' => $nav ),"","logRestEvent.txt");

        try {
            Loader::includeModule('otus.crmtab');

            if (empty($query['ID'])) {
                throw new \Exception("Поле 'ID' обязательно");
            }

            $id = (int)$query['ID'];

            // Проверяем существование перед удалением
            $task = TaskTable::getById($id)->fetch();
            if (!$task) {
                throw new \Exception("Задача с ID {$id} не найдена");
            }

            $deleteResult = TaskTable::delete($id);

            if ($deleteResult->isSuccess()) {
                $event = new Event('otus.crmtab', 'onAfterOtusTaskDelete', ['FIELDS' => ['ID' => $id]]);
                $event->send();

                $result['id']      = $id;
                $result['success'] = true;
            } else {
                throw new \Exception(implode('; ', $deleteResult->getErrorMessages()));
            }

        } catch (\Exception $e) {
            throw new RestException(
                $e->getMessage(),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return $result;
    }

    /**
     * Список задач с фильтрацией и пагинацией.
     *
     * @param array       $query
     * @param int         $nav
     * @param \CRestServer $server
     * @return array
     * @throws RestException
     */
    public static function list($query, $nav, \CRestServer $server): array
    {
        $result = [];

        Debug::writeToFile(array('PARAMS' => $query, 'ACTION' => 'LIST' ),"","logRestEvent.txt");
        Debug::writeToFile(array('NAV' => $nav ),"","logRestEvent.txt");

        try {
            Loader::includeModule('otus.crmtab');

            $navData = static::getNavData($nav, true);
            $limit   = $navData['limit'];
            $offset  = $navData['offset'];
            $page    = (int)($offset / $limit) + 1;

            $filter = [];
            if (!empty($query['filter']) && is_array($query['filter'])) {
                $filter = $query['filter'];
            }

            $order = ['ID' => 'ASC'];
            if (!empty($query['order']) && is_array($query['order'])) {
                $order = $query['order'];
            }

            $tasks = TaskTable::getList([
                'select'      => ['*', 'CLIENT_NAME' => 'CLIENT.NAME', 'PRODUCT_NAME' => 'PRODUCT.NAME'],
                'filter'      => $filter,
                'order'       => $order,
                'limit'       => $limit,
                'offset'      => $offset,
                'count_total' => true,
            ]);

            $items = [];
            while ($task = $tasks->fetch()) {
                $items[] = self::formatTask($task);
            }

            $result['tasks']   = $items;
            $result['total']   = $tasks->getCount();
            $result['page']    = $page;
            $result['limit']   = $limit;
            $result['success'] = true;

        } catch (\Exception $e) {
            throw new RestException(
                $e->getMessage(),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return $result;
    }

    /**
     * Prepare data
     * @param $arguments - data
     * @param $handler - handler
     * @return mixed
     */
    public static function prepareEventData($arguments, $handler)
    {

        Debug::writeToFile(array('arguments'=>$arguments ),"","logRestEvent.txt");
        Debug::writeToFile(array('handler'=>$handler ),"","logRestEvent.txt");

        $events = reset($arguments);
        $response = $events->getParameters();

        Debug::writeToFile(array('response'=>$response ),"","logRestEvent.txt");

        return $response;
    }

    /**
     * Форматирование задачи для вывода в REST-ответе.
     *
     * @param array $task
     * @return array
     */
    private static function formatTask(array $task): array
    {
        return [
            'id'           => (int)$task['ID'],
            'name'         => $task['NAME'],
            'manager'      => $task['MANAGER'],
            'create_date'  => $task['CREATE_DATE'] instanceof Date ? $task['CREATE_DATE']->toString() : null,
            'comment'      => $task['COMMENT'],
            'completed'    => $task['COMPLETED'] === 'Y',
            'client_id'    => !empty($task['CLIENT_ID'])  ? (int)$task['CLIENT_ID']  : null,
            'product_id'   => !empty($task['PRODUCT_ID']) ? (int)$task['PRODUCT_ID'] : null,
            'client_name'  => $task['CLIENT_NAME']  ?? null,
            'product_name' => $task['PRODUCT_NAME'] ?? null,
        ];
    }
}
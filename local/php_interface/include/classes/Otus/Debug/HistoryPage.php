<?php

namespace Otus\Debug;

use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Diag\Debug;

class HistoryPage
{
    const LOG_PATH = '/homework/hw2/debug.log';

    /**
     * @return void
     */
    public static function add(): void
    {

        $page  = Application::getInstance()->getContext()->getRequest()->getRequestUri();
        $dateTime = new DateTime();

        $str = $dateTime->format("Y-m-d H:i:s")."\n";
        $str .= "Открыта страница  $page \n";
        $str .= "---";

        Debug::writeToFile($str, '',static::LOG_PATH);

    }
}
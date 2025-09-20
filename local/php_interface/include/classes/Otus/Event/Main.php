<?php

namespace Otus\Event;

use Bitrix\Main\Context;
use Otus\Debug\HistoryPage;

class Main
{
    public static function handlerPageStart()
    {

        $pages = ['/homework/hw2/debug.php'];
        $request = Context::getCurrent()->getRequest();
        $page = $request->getRequestedPage();

        if(in_array($page, $pages)){
            HistoryPage::add();
        }
    }
}
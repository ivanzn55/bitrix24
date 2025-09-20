<?php
namespace Otus;

use Bitrix\Main\Context;

class Helper
{
    public static function getPathToClassInAdmin(string $class): string
    {
        $result = '';

        if (class_exists($class)) {

            $server = Context::getCurrent()->getServer();
            $documentRoot = $server->getDocumentRoot();
            $reflector = new \ReflectionClass($class);
            $classPath = $reflector->getFileName();

            $result = urlencode(str_replace($documentRoot, '', $classPath));
            $result .= '&full_src=Y&site=s1&lang=ru&&filter=Y&set_filter=Y';

            $result = '/bitrix/admin/fileman_file_edit.php?path='.$result;
        }

        return $result;
    }
}
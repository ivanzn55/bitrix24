<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("ДЗ1: Отладка и логирование");
?>
    <a target="_blank" href="/homework/hw2/error.log">Файл лога</a><br>
<?php
    $a = 10/0;
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
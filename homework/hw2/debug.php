<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("ДЗ1: Отладка и логирование");
?>
<?php
use Otus\Debug\HistoryPage;
HistoryPage::add();
?>

<a target="_blank" href="/homework/hw2/debug.log">Файл лога</a><br>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
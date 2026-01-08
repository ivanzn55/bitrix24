<?php
/**
 * @global  \CMain $APPLICATION
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Валюты");
?><?$APPLICATION->IncludeComponent(
	"otus:currency.rate", 
	".default", 
	[
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CURRENCY_CODE" => "EUR",
		"COMPONENT_TEMPLATE" => ".default"
	],
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

<?php
/**
 * @global  \CMain $APPLICATION
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Врачи");
?><?$APPLICATION->IncludeComponent(
	"custom:doctors.manager",
	"",
	Array(

	)
);?><br>
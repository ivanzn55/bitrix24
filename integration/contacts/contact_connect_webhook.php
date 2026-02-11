<?php

//file_put_contents('webhook.log', print_r($_REQUEST, true). PHP_EOL, FILE_APPEND);

require_once (__DIR__.'/crestcurrent.php');


if(isset($_REQUEST['event'])){
	
	switch($_REQUEST['event']){
		
		// Как я понял достаточно этого вариант для ДЗ
		case 'ONCRMACTIVITYADD':
		
			$activityId = $_REQUEST['data']['FIELDS']['ID'] ?? 0;
			
			if($activityId > 0){
				
				$result = CRest::call('crm.activity.get', [
					'id' => $activityId
				]);
				
				if (!isset($result['error'])) {				
					$contactId = $result['result']['OWNER_ID'];
				}
			}
		
		break;
		
		//Возможно это для чата
		case 'ONIMCONNECTORMESSAGEADD':
		break;
		
		//Возможно это для звонка
		case 'OnExternalCallStart':
		break;
	}
	
	
	if($contactId > 0){
		
		$now = date('Y-m-d\TH:i:sP');
		
		$updateResult = CRest::call('crm.contact.update', [
			'id' => $contactId,
			'fields' => [
				'UF_CRM_LAST_COMM_DATETIME' => $now
			]
		]);
	}
	
}
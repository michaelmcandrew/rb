<?php
require_once('initialise.php');
require_once('functions.php');
date_default_timezone_set('UTC');

//Fetch rows from MYSQL into data object
$select = "SELECT * FROM rb_data.training";

$results =CRM_Core_DAO::executeQuery($select);

$events=array();
while($results->fetch()){
	if (!(trimString($results->col_0) AND trimString($results->col_1) AND trimString($results->col_14))){
		continue;
	}
	
	if (strpos($results->col_14 , ".") !== false){
		continue;
	}
	
	$events[$results->col_12."+++".$results->col_14]=array("title"=>trimString($results->col_12),
											 				"date" => trimString($results->col_14),
															"max_participants" => trimString($results->col_26),
															"custom_9" =>trimString($results->col_16),//total days
															"custom_5" =>trimString($results->col_27),//total costs
															"custom_6" =>trimString($results->col_28),//lunch cost
															"custom_8" =>trimString($results->col_29),//Budget
															// 
															// event shoudl not be piblice!!!!!
															);
	$i++;
//	if ($i==7) { break; }8
}
foreach ($events as $event){
	$params=array();
  print_r($event['title']."\n");
	 print_r($event['date']."\n");
	// print_r($event['max_participants']."\n");
	// print_r("\n");

	$params=array ('version' =>'3',
				'start_date' => converttheDate($event['date']),
				'event_type_id' => '6',
				'title' => $event['title'],
				'is_active' => '1',
				'max_participants' => $event['max_participants'],
				'custom_9' => $event['custom_9'],
				'custom_5' => $event['custom_5'],
				'custom_6' => $event['custom_6'],
				'custom_8' => $event['custom_8'],
				);
	$event_results=civicrm_api("Event","create", $params);
	print_r($event_results['id']."	");
	handle_errors($event_results);
	
}


// ksort($events);
//print_r($events);

//print_r($results);
	
	

?>
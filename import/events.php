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
	
	$events[$results->col_12."+++".$results->col_14]=array(
															"title"=>trimString($results->col_12),
											 				"date" => trimString($results->col_14),
											 				"start_time" => trimString($results->col_19),
											 				"end_time" => trimString($results->col_20),
															"max_participants" => trimString($results->col_26),
															"custom_7" =>trimString($results->col_16),//total days
															"custom_5" =>trimString($results->col_27),//total costs
															"custom_6" =>trimString($results->col_28),//lunch cost
															"custom_4" =>trimString($results->col_23),//accessible
															"custom_8" =>trimString($results->col_24),//lunch included?
															"custom_9" =>trimString($results->col_17),//training days
															"custom_10" =>trimString($results->col_15),//tutor
															"custom_11" =>trimString($results->col_25),//organiser
															);
	$i++;
//	if ($i==7) { break; }8
}
foreach ($events as $event){
	$params=array();
  // print_r($event['title']."\n");
  // 	 print_r($event['date']."\n");
	// print_r($event['max_participants']."\n");
	// print_r("\n");

	$params=array ('version' =>'3',
				'start_date' => converttheDate($event['date']),
				'event_type_id' => '6',
				'title' => $event['title'],
				'is_active' => '1',
				'max_participants' => $event['max_participants'],
				'custom_7' => $event['custom_7'],
				'custom_5' => $event['custom_5'],
				'custom_6' => $event['custom_6'],
				'is_online_registration' => '0',
				'custom_9' => $event['custom_9'],
				'custom_10' => $event['custom_10'],
				'custom_11' => $event['custom_11'],
				);
	if ($event['custom_4'] == "Yes"){
		$params['custom_4'] = 1;
	}
	if ($event['custom_8'] == "Yes"){
		$params['custom_8'] = 1;
	}

	$event_results=civicrm_api("Event","create", $params);
	print_r($event_results['id']."	");
	handle_errors($event_results);
	
}
print_r("\n");
print_r("TOTAL:  ".$i."\n");

// ksort($events);
//print_r($events);

//print_r($results);
	
	

?>
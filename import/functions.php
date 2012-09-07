<?php

function trimString($string){
	trim($string);
	return $string;
}

function handle_errors($result,$params = NULL){
	if($result['is_error']){
		print_r($result);
		print_r($params);
	}
}

function converttheDate($date){
	$date=trimString($date);
	$date=str_replace("/","-",$date);
	return $date;
}

function eventSearch($event_title, $date){
	
	$event_params=array ('version=3',
	'sequential' =>'1',
	'title' => $event_title);
	
	$event_results=civicrm_api("Event","get", $event_params);
	return $event_results['id'];
}

?>
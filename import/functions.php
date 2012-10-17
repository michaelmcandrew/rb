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
	if(!($event_title AND $date)){
		print_r("ERROR:  no title or date");exit;
	}
	$date = converttheDate($date);
	
	$event_params=array ('version'=> 3,
						'sequential' =>'1',
						'title' => $event_title,
						'register_date' => null,				
						'event_start_date' => $date);
	
	$event_results=civicrm_api("Event","get", $event_params);
	handle_errors($event_results, $event_params);
	
	return $event_results['id'];
}

function registerParticipant($contactId,$eventId,$statusId){
	if(!($contactId AND $eventId AND $statusId)){
		print_r("ERROR:  missing IDs");exit;
	}
		$participant_params=array ('version'=> 3,
							'event_id' => $eventId,
							'status_id' => $statusId,
							'contact_id' => $contactId);
		$participant_results=civicrm_api("Participant","create", $participant_params);
		print_r(" P".$participant_results['id']."  ");
		handle_errors($participant_results, $participant_params);
}
?>
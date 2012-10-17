<?php
require_once('initialise.php');

//remove (initial 10) contacts that were created
$select = "SELECT * FROM civicrm_participant";

$results = CRM_Core_DAO::executeQuery($select);
//print_r($results);exit;
while($results->fetch()){
	$ids[]=$results->id;
}

foreach($ids as $id){
	$params=array('id' => $id, 'skip_undelete' => true, 'version'=>3);
	$participant_delete=civicrm_api('Participant', 'delete', $params);
	print_r("P".$id." D ");
}



$select = "SELECT * FROM civicrm_contact WHERE source = 'event data'";

$results = CRM_Core_DAO::executeQuery($select);
//print_r($results);exit;
while($results->fetch()){
	$ids[]=$results->id;
}

foreach($ids as $id){
	$params=array('id' => $id, 'skip_undelete' => true, 'version'=>3, 'contact_type' => "Individual");
	$contact_delete=civicrm_api('Contact', 'delete', $params);
	print_r("C".$id."D ");
}
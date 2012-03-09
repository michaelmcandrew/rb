<?php
require_once('initialise.php');

//remove (initial 10) contacts that were created
$select = "SELECT * FROM civicrm_contact WHERE id > 4";

$results = CRM_Core_DAO::executeQuery($select);
//print_r($results);exit;
while($results->fetch()){
	$ids[]=$results->id;
}

foreach($ids as $id){
	$params=array('id' => $id, 'skip_undelete' => true, 'version'=>3, 'contact_type' => "Organization");
	$contact_delete=civicrm_api('Contact', 'delete', $params);
	print_r("D ".$id."D ");
}
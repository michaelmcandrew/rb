<?php
require_once('initialise.php');
require_once('functions.php');


//Fetch rows from MYSQL into data object
$select = "SELECT * FROM rb_data.training";

$results =CRM_Core_DAO::executeQuery($select);
$i=0;



while($results->fetch()){
	if (!(trimString($results->col_0) AND trimString($results->col_1))){
		continue;
	}
	if (!(trimString($results->col_5))){
		continue;
	}
	print_r($results->_id." ".$results->col_0." ".$results->col_1."\n");

	$params= array('version' =>'3',
	'email' => trimString($results->col_5),
	'sequential' => '1');
	$email_results=civicrm_api("Email","get",$params);
	if ($email_results['count']!=0){
		print_r($email_results);

		//call to event search function send date, title, get back id
		
		//add event to existing contact
	}
	else{
		print_r("NO email create contact"."\n");

		//create a new contact

		//call to event search function send date, title, get back id

		// add event to said contact


	}


	$i++;
	if ($i==2) { break; }
}


print_r($i."\n");

function createAddress($cid,$results){
	//print_r($results);exit;
	if(!($cid OR $results)){
		return;
	}	
	$addressParams=array('version' =>'3',
	'contact_id' => $cid,
	'location_type_id' => '2',
	'street_address' => trimString($results->col_5),
	'supplemental_address_1' =>trimString($results->col_6),
	'city' =>trimString($results->col_7),
	'postal_code' => trimString($results->col_8),
	'country_id' =>'1226',
);
$address_create=civicrm_api("Address","create", $addressParams);

handle_errors($address_create);
}

function createPhone($cid,$results,$ptid,$phone,$isPrimary = "0"){
	// 	print_r($results);exit;
	if(!($cid && $results && $ptid && $phone)){
		echo " p- ";
		return;
	}
	echo " p+ ";
	$phoneParams=array('version' =>'3',
	'contact_id' => $cid,
	'phone_type_id' => $ptid,
	'location_type_id' => '2',
	'is_primary' => $is_primary,
	'phone' => $phone,
);
$phone_create=civicrm_api("Phone","create", $phoneParams);
handle_errors($phone_create);
}

function createWebsite($cid,$url){
	// 	print_r($results);exit;
	if(!($cid && $url)){
		echo " w- ";
		return;
	}
	if (!(strpos($url,"@") === false))
	{
		echo " wE ";
		return;
	}

	$beginning=explode("//", $url);
		//print_r($beginning);exit;
	if ($beginning[0] != "http:" OR "https:"){
		$url = "http://".$url;
	}
	echo " w+ ";
	$websiteParams=array('version' =>'3',
	'contact_id' => $cid,
	'website_type_id' => '2',
	'url' => $url,
);
$website_create=civicrm_api("Website","create", $websiteParams);
handle_errors($website_create);
}

function createEmail($cid,$email){
	// 	print_r($results);exit;
	if(!($cid && $email)){
		echo " e- ";
		return;
	}
	if ((strpos($email,"@") === false))
	{
		echo " eE ";
		return;
	}
	echo " e+ ";
	$emailParams=array('version' =>'3',
	'contact_id' => $cid,
	'location_type_id' => '2',
	'is_primary' => '1',
	'email' => $email,
);
$email_create=civicrm_api("Email","create", $emailParams);
handle_errors($email_create);
}



// http://rb.local/civicrm/ajax/doc/api#/civicrm/ajax/rest?json=1&debug=1&version=3&entity=Contact&action=create&contact_type=Organization

?>
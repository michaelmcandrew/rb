<?php
require_once('initialise.php');
require_once('functions.php');

//Fetch rows from MYSQL into data object
$select = "SELECT * FROM rb_data.training";

$results =CRM_Core_DAO::executeQuery($select);
$i=0;

while($results->fetch()){
	$firstname = trimString($results->col_0);
	$lastname = trimString($results->col_1);
	$email = trimString($results->col_5);
	$event_title=trimString($results->col_12);
	$event_date=trimString($results->col_14);
	$phone = trimString($results->col_4);
	$organisation = trimString($results->col_2);
	$address = trimString($results->col_3);
	

	if (!( $firstname AND $lastname AND $event_title AND  $event_date)){
		continue;
	}
	if (!$email){
		continue;
	}
	// print_r("\n");
	// 	print_r($results->_id." ".$firstname." ".$lastname."\n");

	$params = array('version' =>'3',
	'email' => $email,
	'first_name' => $firstname,
	'last_name' => $lastname,
	'sequential' => '1');
	$contact_results = civicrm_api("Contact","get",$params);

	if ($contact_results['count']!=0){
		//found existing contact
		$eventId = eventSearch($event_title,$event_date);
		if (!$eventId) { 
			print_r("ERROR: Missing event ID!!!");
			exit; 
		}
		//print_r($contact_results);
		registerParticipant($contact_results['values']['0']['contact_id'],$eventId);

	}
	else{
		$eventId = eventSearch($event_title,$event_date);

		//create a new contact
		$contact_params = array( 
			'first_name' => $firstname,
			'last_name' => $lastname,
			'contact_type' => 'Individual',
			'organization_name' => $organisation,
			'source' => 'event data',
			'email' => $email,
			'version' => 3,
			'api.participant.create' => array( 
				'event_id' => $eventId,
			),
			'api.phone.create' => array( 
				'phone' => $phone,
				'location_type_id' => '3',
				'phone_type_id' => '1',
			),
			'api.address.create' => array( 
				'address' => $address,
				'location_type_id' => '3',
			),
		);
		// print_r($contact_params);
		$contact_results = civicrm_api("Contact","create",$contact_params);
		print_r("C".$contact_results['id']."  ");
		handle_errors($contact_results);

	}


	$i++;
	//if ($i==10) { break; }
}

print_r("\n");
print_r("TOTAL:  ".$i."\n");

// function createAddress($cid,$results){
	// 	//print_r($results);exit;
	// 	if(!($cid OR $results)){
		// 		return;
		// 	}	
		// 	$addressParams=array('version' =>'3',
		// 	'contact_id' => $cid,
		// 	'location_type_id' => '2',
		// 	'street_address' => trimString($results->col_5),
		// 	'supplemental_address_1' =>trimString($results->col_6),
		// 	'city' =>trimString($results->col_7),
		// 	'postal_code' => trimString($results->col_8),
		// 	'country_id' =>'1226',
		// );
		// $address_create=civicrm_api("Address","create", $addressParams);
		// 
		// handle_errors($address_create);
		// }
		// 
		// function createPhone($cid,$results,$ptid,$phone,$isPrimary = "0"){
			// 	// 	print_r($results);exit;
			// 	if(!($cid && $results && $ptid && $phone)){
				// 		echo " p- ";
				// 		return;
				// 	}
				// 	echo " p+ ";
				// 	$phoneParams=array('version' =>'3',
				// 	'contact_id' => $cid,
				// 	'phone_type_id' => $ptid,
				// 	'location_type_id' => '2',
				// 	'is_primary' => $is_primary,
				// 	'phone' => $phone,
				// );
				// $phone_create=civicrm_api("Phone","create", $phoneParams);
				// handle_errors($phone_create);
				// }
				// 
				// function createWebsite($cid,$url){
					// 	// 	print_r($results);exit;
					// 	if(!($cid && $url)){
						// 		echo " w- ";
						// 		return;
						// 	}
						// 	if (!(strpos($url,"@") === false))
						// 	{
							// 		echo " wE ";
							// 		return;
							// 	}
							// 
							// 	$beginning=explode("//", $url);
							// 		//print_r($beginning);exit;
							// 	if ($beginning[0] != "http:" OR "https:"){
								// 		$url = "http://".$url;
								// 	}
								// 	echo " w+ ";
								// 	$websiteParams=array('version' =>'3',
								// 	'contact_id' => $cid,
								// 	'website_type_id' => '2',
								// 	'url' => $url,
								// );
								// $website_create=civicrm_api("Website","create", $websiteParams);
								// handle_errors($website_create);
								// }
								// 
								// function createEmail($cid,$email){
									// 	// 	print_r($results);exit;
									// 	if(!($cid && $email)){
										// 		echo " e- ";
										// 		return;
										// 	}
										// 	if ((strpos($email,"@") === false))
										// 	{
											// 		echo " eE ";
											// 		return;
											// 	}
											// 	echo " e+ ";
											// 	$emailParams=array('version' =>'3',
											// 	'contact_id' => $cid,
											// 	'location_type_id' => '2',
											// 	'is_primary' => '1',
											// 	'email' => $email,
											// );
											// $email_create=civicrm_api("Email","create", $emailParams);
											// handle_errors($email_create);
											// }



											// http://rb.local/civicrm/ajax/doc/api#/civicrm/ajax/rest?json=1&debug=1&version=3&entity=Contact&action=create&contact_type=Organization

											?>
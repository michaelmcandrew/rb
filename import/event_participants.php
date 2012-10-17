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
	$phone = str_replace(' ', '', trimString($results->col_4));
	$organisation = trimString($results->col_2);
	$address = trimString($results->col_3);
	$attended = trimString($results->col_9);
	
	if ($attended == 'Yes'){
		$attended = 2;
	}elseif($attended == 'No'){
		$attended = 3;
	}else{
		$attended = 1;
	}

	if (!( $firstname AND $lastname AND $event_title AND  $event_date)){
		continue;
	}
	if (!$email){
		continue;
	}
	// print_r("\n");
	// 	print_r($results->_id." ".$firstname." ".$lastname."\n");
	$params = '';
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
		registerParticipant($contact_results['values']['0']['contact_id'],$eventId,$attended);

	}
	else{
		$eventId = eventSearch($event_title,$event_date);
		$contact_params = '';
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
				'status_id' => $attended,
				'register_date' => null,				
			),
		);
		if ($phone){
			if (strlen($phone) > '32'){
				print_r("\n"."phone too long"."\n");
				continue;
			}
				$contact_params ['api.phone.create'] = array( 
					'phone' => $phone,
					'location_type_id' => '3',
					'phone_type_id' => '1',
				);
				print_r("p");
				
			}
			if ($address){
				if (strlen($address) > '96'){
					print_r("\n"."address too long"."\n");
					continue;
				}
				$contact_params['api.address.create'] = array( 
					'street_address' => $address,
					'location_type_id' => '3',
				);
			}
		// cax print_r($contact_params);
		$contact_results = civicrm_api("Contact","create",$contact_params);
		print_r("C".$contact_results['id']."  ");
		handle_errors($contact_results);
	}
	$i++;
	//if ($i==10) { break; }
}

print_r("\n");
print_r("TOTAL:  ".$i."\n");

?>
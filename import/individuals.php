<?php
require_once('initialise.php');
require_once('functions.php');


//Fetch rows from MYSQL into data object
$select = "SELECT * FROM rb_data.contacts";

$results =CRM_Core_DAO::executeQuery($select);
$i=0;

$prefix_translation = array(
	'Revd' => 'Rev',
	'Sr'=>'Sister',
	'Cpt'=>'Captain',
	'Fr'=>'Father',
	'Pr'=>'Pastor',
	);

$prefix2id=array(
	'Mrs'=>'1',
	'Ms'=>'2',
	'Mr'=>'3',
	'Dr'=>'4',
	'Miss'=>'5',
	'Chair'=>'6',
	'Cllr'=>'7',
	'Captain'=>'8',
	'Father'=>'9',
	'Lieutenant'=>'10',
	'Pastor'=>'11',
	'Rabbi'=>'12',
	'Rev'=>'13',
	'Sgt'=>'14',
	'Sister'=>'15'
	);

while($results->fetch()){
		//Org columns: 4 5 6 7 8 9 10 13
		$params = array();
		//print_r($results->col_4." 4| ".$results->col_5." 5| ".$results->col_6." 6| ".$results->col_7." 7| ".$results->col_8." 8| ".$results->col_9." 9| ".$results->col_10." 10| ".$results->col_13."13 \n");
		$firstname = trimString($results->col_1);
		$lastname = trimString($results->col_2);
		if(!($firstname OR $lastname)){
			continue;
		}
		
		$params=array(
			'version' =>'3',
			'contact_type' =>'Individual',
			'first_name' => $firstname,
			'last_name' => $lastname,
			'job_title' => trimString($results->col_3),
			'current_employer' => trimString($results->col_4),
			'external_identifier' => 'i'.$results->_id
		);
		if($results->col_0){
			$params['prefix']=str_replace('.','' , $results->col_0 );
			$params['prefix']=$prefix_translation[$params['prefix']];
			$params['prefix_id']=$prefix2id[$params['prefix']];
		}	
		$contact_create=civicrm_api("Contact","create", $params);
		
		print_r($contact_create['id']." ");
		
		handle_errors($contact_create, $params);

		createAddress($contact_create['id'],$results);
		
		createPhone($contact_create['id'],$results,'1',trimString($results->col_9),'1');//Phone
		
		createPhone($contact_create['id'],$results,'3',trimString($results->col_10));//FAX
		
		createPhone($contact_create['id'],$results,'2',trimString($results->col_11));//Mobile
		
		createEmail($contact_create['id'],trimString($results->col_12));
		
		createWebsite($contact_create['id'],trimString($results->col_13));

		echo "	";
		
			$i++;
	//	if ($i==200) { break; }
}

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
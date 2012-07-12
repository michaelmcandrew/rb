<?php
require_once('initialise.php');
require_once('functions.php');


//Fetch rows from MYSQL into data object
$select = "SELECT * FROM rb_data.contacts WHERE Col_18='Yes'";

$results =CRM_Core_DAO::executeQuery($select);
$i=0;



//get a translation from external ID to internal ID	

$serviceAreaTranslation = array(
	'Advice/info' => 'Advice/advocacy',
	'Advocacy' => 'Advice/advocacy',
	'Self-advocacy' => 'Advice/advocacy',
	'Debt' => 'Advice/advocacy',
	'Animals' => 'Animals',
	'Refugees' => 'BAMER',
	'Translation' => 'BAMER',
	'BME' => 'BAMER',
	'Gypsies/travellers' => 'BAMER',
	'Carers' => 'Carers',
	'Children' => 'Children/young people',
	'Parents' => 'Children/young people',
	'Play' => 'Children/young people',
	'Under 5s' => 'Children/young people',
	'Young people' => 'Children/young people',
	'Community Association' => 'Community development',
	'Community devt' => 'Community development',
	'Infrastructure' => 'Community development',
	'Regeneration ' => 'Community development',
	'Representation' => 'Community development',
	'Crime/disorder' => 'Crime/disorder',
	'Environment' => 'Environment',
	'Recycling ' => 'Environment',
	'Religion' => 'Faith',
	'Faith' => 'Faith',
	'Homelessness' => 'Housing',
	'Sheltered accom' => 'Housing',
	'Tenants/residents' => 'Housing',
	'Housing' => 'Housing',
	'Learning disabilities' => 'Learning difficulties',
	'Gay' => 'LGBT',
	'Leisure' => 'Leisure/culture',
	'Local history' => 'Leisure/culture',
	'Sports' => 'Leisure/culture',
	'Arts' => 'Leisure/culture',
	'Support' => 'Mental wellbeing',
	'Counselling' => 'Mental wellbeing',
	'Mental health' => 'Mental wellbeing',
	'Older people' => 'Older people',
	'Older People’s Forum' => 'Older people',
	'Health ' => 'Physical health/wellbeing',
	'Patients’ groups ' => 'Physical health/wellbeing',
	'Substance misuse' => 'Substance misuse',
	'Education' => 'Training/education',
	'Training' => 'Training/education',
	'Transport' => 'Transport',
	'Women' => 'Women'
);
$org_type_translation = array(
	'Charity shops' => 'Charity shop',
	'Funder' => 'Funder',
	'Trust' => 'Funder',
	'LBR Councillor' => 'LBR Councillor',
	'Local authority' => 'Local govt not LBR',
	'Press' => 'Media',
	'PCT' => 'NHS service/officer',
	'Political' => 'Political party',
	'Schools' => 'School',
	'Extended schools' => 'School'
);
$mailing_list_translation = array(
	'Network' => 'Network',
	'NetEMAIL' => 'NetEMAIL',
	'eCircular' => 'eCircular'
);

while($results->fetch()){
		
	if ($results->col_14 OR $results->col_15){
		$categories = explode(chr(11), trimString($results->col_14));
		$mailingLists = explode(chr(11), trimString($results->col_15));
		$cid=external2internal($results->_id);
		$params=array();
		$params['version']="3";
		$params['entity_id']=$cid;
		$params['custom_1']=translateCategories($serviceAreaTranslation,$categories);
		$params['custom_2']=translateCategories($org_type_translation,$categories);
		$params['custom_3']=translateCategories($mailing_list_translation,$mailingLists);
		$addCategory=civicrm_api("CustomValue","create", $params);
		handle_errors($addCategory);
		print_r($cid." ".count($params['custom_1'])." ".count($params['custom_2'])." ".count($params['custom_3'])."\n");
	}
}
function external2internal($externalId){
	$externalId="o".$externalId;
	$params=array (
	'version' =>'3',
	'external_identifier' => $externalId
	);
$results=civicrm_api("Contact","get",$params);
//print_r($results);exit;

return $results['id'];

}

function translateCategories($translation,$categories){
	$translated=array();
	foreach($categories as $category){
		if(in_array($category, array_keys($translation))){
			$translated[$translation[$category]]=$translation[$category];
		}
	}
	return $translated;
}


function addOrgTypes($cid,$categories){
	
}

?>

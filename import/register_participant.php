<?php
require_once('initialise.php');
require_once('functions.php');

$params=array('title' => 'Self Harm',
			'version'=>'3',
			);
			
$results=civicrm_api('event','get',$params );
print_r($results);

?>
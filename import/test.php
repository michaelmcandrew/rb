<?php

require_once('initialise.php');
$cats = array('Sports','Carers');
print_r($cats);
$results=civicrm_api("CustomValue","create", array('version' =>'3','entity_id' => 1 ,'custom_1' => $cats));

print_r($results);



?>
<?php

function trimString($string){
	trim($string);
	return $string;
}

function handle_errors($result){
	if($result['is_error']){
		print_r($result);
	}
}

function convertDate($date){
	$date=trimString($date);
	$date=str_replace("/","-",$date);
	return $date;
}

?>
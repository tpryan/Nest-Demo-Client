<?php
	
	require "lib/Nest/nest.php";
	$access_token = $_COOKIE['nest_token'];
	$nest = new Nest();
	$nest->setAccessToken($access_token);


	list($target,$id,$value) = explode("|", $_GET['call']);


	if ($target == "structure") {
		$result =  $nest->setStructureAway($id, $value);
	} else if ($target == "thermostat") {
		$result =  $nest->setThermostatTargetTemperature($id, "f", $value);
	}


	echo $result;
?>
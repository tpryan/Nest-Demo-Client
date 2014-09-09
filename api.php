<?php
	
	require "lib/Nest/nest.php";
	$access_token = $_COOKIE['nest_token'];
	$nest = new Nest();
	$nest->setAccessToken($access_token);


	list($target,$id,$value) = explode("|", $_GET['call']);


	if ($target == "structure") {
		$result =  $nest->setStructureSetting($id, "away", $value);
	} else if ($target == "thermostat") {
		$result =  $nest->setThermostatSetting($id, "target_temperature_f", $value);
	}


	echo $result;
?>
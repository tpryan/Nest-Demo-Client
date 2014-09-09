<?php
require "config/settings.php";
require "lib/Nest/nest.php";

$access_token = $_COOKIE['nest_token'];

if (strlen($access_token) == 0) {
	header("Location: " . $token_url );
	die();
}



$nest = new Nest();
$nest->setAccessToken($access_token);
$structures_data = $nest->getStructures();
$devices_data = $nest->getDevices();





?>

<!DOCTYPE html>
    <html lang="en">
    <head>
    	<meta charset="UTF-8">
    	<title>Document</title>
    
	    <script type="text/javascript">

	    	document.addEventListener('DOMContentLoaded', function() {
	   			[].forEach.call( document.querySelectorAll('input[name="away"]'), function(el) {
				   el.addEventListener('click', processAwayChange, false);
	 			});
	 			[].forEach.call( document.querySelectorAll('input[type="text"]'), function(el) {
				   el.addEventListener('change', processDeviceChange, false);
	 			});
			});


	    	function processAwayChange() {
	    		var call = document.querySelector('input[name="away"]:checked').id;
	    		var req = new XMLHttpRequest();
				req.open("GET", "api.php?call=" + call ,true);
				req.send(null);

	    	}

	    	function processDeviceChange(e) {
	    		var call = e.target.name;
	    		call = call.replace("target-temp", e.target.value);
	    		var req = new XMLHttpRequest();
				req.open("GET", "api.php?call=" + call ,true);
				req.send(null);
	    	}


	    </script>
	    <link href='http://fonts.googleapis.com/css?family=Lato|Oswald' rel='stylesheet' type='text/css'>
	    <link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>

		<style type="text/css">
			h1{
				font-family: 'Oswald', sans-serif;
			}
			body{
				font-family: 'Lato', sans-serif;
			}

		</style>

	</head>
<body>
    	
<?php 
	
	foreach ($structures_data as $structure){
		echo "<h1>" . $structure->name. "</h1>\n";

		$away_selected = "";
		$home_selected = "";

		if ($structure->away == 'home') {
			$home_selected = 'checked="checked" ';
		} else{
			$away_selected = 'checked="checked" ';
		}

		echo '<label><input type="radio" name="away" ' . $home_selected . 'id="structure|' . $structure->structure_id . '|home" value="home">Home</label>';
		echo '<label><input type="radio" name="away" ' . $away_selected . 'id="structure|' . $structure->structure_id . '|away" value="away">Away</label><br />';

	}

	echo "<br /><br />";

	foreach ($devices_data->thermostats as $device){
		echo "<h1>" . $device->name. "</h1>\n";
		echo '<label>Target Temp</label><input type="text"  name="thermostat|' . $device->device_id .'|target-temp" value="' .$device->target_temperature_f  . '"><br />';
		

	}


?>

    </body>
    </html>    
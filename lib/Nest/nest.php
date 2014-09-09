<?php

	class Nest {

		protected $access_token;
		private $base_url = "https://developer-api.nest.com";
		private $auth_url = "https://api.home.nest.com/oauth2/access_token";

	    function Nest(){
	        return $this;
	    }

	    function getStructures(){
	    	$api_url = $this->base_url .  "/structures.json?auth=" . $this->access_token;
	    	return json_decode(file_get_contents($api_url, false));
	    }

	    function getDevices(){
	    	$api_url = $this->base_url .  "/devices.json?auth=" . $this->access_token;
	    	return json_decode(file_get_contents($api_url, false));
	    }

	    function setThermostatSetting($device_id, $setting, $value) {
	    	$path = '/devices/thermostats/' . $device_id . "/" . $setting . "/?auth=" . $this->access_token;
	    	$url =  $this->base_url . $path;
	    	$result = $this->makeHTTPPPut($url, $value);
			return $result;
	    }

	    function setStructureSetting($structure_id, $setting, $value) {
	    	$path = '/structures/' . $structure_id . "/" . $setting . "/?auth=" . $this->access_token;
	    	$url =  $this->base_url . $path;
	    	$result = $this->makeHTTPPPut($url, $value);
			return $result;
	    }

	    protected function makeHTTPPPut($url, $value){
	    	if (is_bool($value)){
	    		$value_to_use = ($value) ? 'true' : 'false';
	    	} else if (is_numeric($value)) {
	    		$value_to_use = (string) $value;
	    	} else if (is_string($value)) {
	    		$value_to_use = '"' . $value . '"';
	    	}

	    	//So there is some weirdness here. For some reason some part of the request was not being fowarded 
	    	//when you set follow_location to true.  So I'm manually redirecting here to ensure both the 'PUT' verb
	    	//and the data continue on. Not sure where the problem lies.  But I will probably have to revist.  
		    $context = [
			  'http' => [
			   	'header'=>"Content-Type: application/json\r\n" . "Accept: */*\r\n" . "User-Agent: curl/7.30.0" ,
			    'method' => 'PUT',
			    'protocol_version' => "1.1",
			    'follow_location' => false,
			    'content' => $value_to_use
			  ]
			];

			//$url = "http://localhost:10080/test.php";
			//echo 'curl -v -L -X PUT "' . $url . '" -H "Content-Type: application/json" -d "'. $value_to_use .'"';

			$context = stream_context_create($context);
			$result = json_decode(file_get_contents($url, false, $context));

			//TODO: Write better handling here. 
			$next_location = str_replace("Location: ", "", $http_response_header[4]) ;
			$result = json_decode(file_get_contents($next_location, false, $context));

			return $result;

	    }

	    function getAuthToken($client_id, $client_secret, $code) {

			$url =  $this->auth_url . 
					"?code=" . $code . 
					"&client_id=" . $client_id . 
					"&client_secret=" . $client_secret . 
					"&grant_type=authorization_code";

		    $context = [
			  'http' => [
			    'method' => 'POST',
			    'header'  => 'Content-type: application/x-www-form-urlencoded'
			  ]
			];

			$context = stream_context_create($context);
			$r = file_get_contents($url, false, $context);
			$returned_items = json_decode($r);

			return $returned_items->access_token;
	    }


	    function setAccessToken($access_token) {
	    	$this->access_token = $access_token;
	    }


	}	



?>
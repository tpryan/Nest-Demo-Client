<?php

	class Nest {

		protected $access_token;
		private $base_url = "https://developer-api.nest.com";
		private $auth_url = "https://api.home.nest.com/oauth2/access_token";
		private $attempts;

		/**
		 * Constructor
		 * @param numeric $attempts The number of times to follow a 307 redirect. 
		 * @return Nest
		 */
	    function Nest($attempts=3){
	    	$this->attempts = $attempts;
	        return $this;
	    }

	    /**
	     * A function for creating free form queries of the API path
	     * @param string $path 
	     * @return string
	     */
	    function queryPath($path) {
	    	$auth_path = "?auth=" . $this->access_token;
	    	$api_url = $this->base_url . $path . $auth_path;
	    	$results = json_decode(file_get_contents($api_url, false));
	    	return $results;
	    }

	    /**
	     * A shortcut function to get all of the structures. 
	     * @return string
	     */
	    function getStructures(){
	    	$path = "/structures";
	    	return $this->queryPath($path);
	    }

	    /**
	     * A shortcut function to get all of the devices. 
	     * @return string
	     */
	    function getDevices(){
	    	$path = "/devices";
	    	return $this->queryPath($path);
	    }

	    /**
	     * A shortcut function to get all of the thermostats. 
	     * @return string
	     */
	    function getThermostats(){
	    	$path = "/devices/thermostats";
	    	return $this->queryPath($path);
	    }

	    /**
	     * A shortcut function to get all of the alarms. 
	     * @return string
	     */
	    function getAlarms(){
	    	$path = "/devices/smoke_co_alarms";
	    	return $this->queryPath($path);
	    }

	    /**
	     * Sets the target temperature on a given thermostat
	     * @param string $device_id unique id for a given thermostat
	     * @param string $scale f or c
	     * @param numeric $value 
	     * @param string $highOrLow optional
	     * @return string
	     */
	    function setThermostatTargetTemperature($device_id, $scale, $value, $highOrLow = ""){
	    	$setting = "target_temperature_";
	    	if (($highOrLow == "high") || ($highOrLow == "low")) {
	    		$setting .= $highOrLow . "_";
	    	}
	    	$setting .= $scale;

	    	return $this->setThermostatSetting($device_id, $setting, $value);
	    }

	    /**
	     * Sets's the fan on or off.
	     * @param string $device_id unique id for a given thermostat
	     * @param boolean $value 
	     * @return string
	     */
	    function setThermostatFanTimerActive($device_id, $value){
	    	$setting = "fan_timer_active";
	    	return $this->setThermostatSetting($device_id, $setting, $value);
	    }

	    /**
	     * Sets heating/cooling or off states. 
	     * @param string $device_id unique id for a given thermostat
	     * @param string $mode options are "heat", "cool", "heat-cool", "off"
	     * @return string
	     */
	    function setThermostatHVACMode($device_id, $mode){
	    	$setting = "hvac_mode";
	    	return $this->setThermostatSetting($device_id, $setting, $mode);
	    }


	    /**
	     * Executes settings changes on thermostat.  Set it to be protected because very little of the 
	     * settings are writeable. So I figured I would explicitly define an interface rather than leaving it up 
	     * to the front end to create well formed settings changes.
	     * @param string $device_id unique id for a given thermostat
	     * @param string $setting thermostat setting to tweak
	     * @param any $value 
	     * @return string
	     */
	    protected function setThermostatSetting($device_id, $setting, $value) {
	    	$auth_path = "/?auth=" . $this->access_token;
	    	$path = '/devices/thermostats/' . $device_id . "/" . $setting . $auth_path;
	    	$url =  $this->base_url . $path;
	    	$result = $this->makeHTTPPPut($url, $value);
			return $result;
	    }


	    /**
	     * Sets structure to home or away. 
	     * @param string $structure_id unique id for a given structure
	     * @param string $mode options are "home" or  "away"
	     * @return string
	     */
	    function setStructureAway($structure_id, $mode){
	    	$setting = "away";
	    	return $this->setStructureSetting($structure_id, $setting, $mode);
	    }

	    function setStructureETA($structure_id, $trip_id, $eta_start_range, $eta_end_range){
	    	$eta_start = date("c",strtotime($eta_start_range));
			$eta_end = date("c",strtotime($eta_end_range));
			$eta_post = '{"trip_id":"' . $trip_id . 
						'","estimated_arrival_window_begin":"'.$eta_start_range.
						'","estimated_arrival_window_end":"'.$eta_end_range.'"}';

			$results = $this->setStructureSetting($structure_id,"eta", $eta_post);
			return $results;
	    }


	     /**
	     * Executes settings changes on structure.  Set it to be protected because very little of the 
	     * settings are writeable. So I figured I would explicitly define an interface rather than leaving it up 
	     * to the front end to create well formed settings changes.
	     * @param string $structure unique id for a given thermostat
	     * @param string $setting structure setting to tweak
	     * @param any $value 
	     * @return string
	     */
	    function setStructureSetting($structure_id, $setting, $value) {
	    	$auth_path = "/?auth=" . $this->access_token;
	    	$path = '/structures/' . $structure_id . "/" . $setting . $auth_path;
	    	$url =  $this->base_url . $path;
	    	$result = $this->makeHTTPPPut($url, $value);
			return $result;
	    }

	    /**
	     * Handles the HTTP Put command since it's finicky and tricky to get right. 
	     * @param string $url Complete url to access Nest REST resource.
	     * @param string $value The value to set at the given url location.
	     * @return string JSON results from Nest.
	     */
	    protected function makeHTTPPPut($url, $value){
	    	
	    	if ($this->isJSONObject($value)){
	    		$value_to_use = $value;
	    	} else if (is_bool($value)){
	    		$value_to_use = ($value) ? 'true' : 'false';
	    	} else if (is_numeric($value)) {
	    		$value_to_use = (string) $value;
	    	} else if (is_string($value)) {
	    		$value_to_use = '"' . $value . '"';
	    	}

	    	//So there is some weirdness here. For some reason some part of the request was not being fowarded 
	    	//when you set follow_location to true.  So I'm manually redirecting here to ensure both the 'PUT' verb
	    	//and the data continue on. Not sure where the problem lies.  But I will probably have to revist.  
	    	//ignore_errors allows me to pass 400 errors that are expected conditions back to the caller. 
		    $context = [
			  'http' => [
			   	'header'=>"Content-Type: application/json\r\n" . "Accept: */*\r\n" . "User-Agent: Nest Demo Client" ,
			    'method' => 'PUT',
			    'protocol_version' => "1.1",
			    'follow_location' => false,
			    'ignore_errors' => true,
			    'content' => $value_to_use
			  ]
			];

			$status_code = 0;
			$attempts = 0;
			$context = stream_context_create($context);

			while (($status_code != 200) && $attempts <= $this->attempts ){
				$attempts++;
				$result = json_decode(@file_get_contents($url, false, $context));
				$response_headers = $this->processHeaders($http_response_header);
				$status_code = $response_headers['status_code'];
				$url =  $response_headers['location'];
				if ($status_code == 400){
					break;
				}

			}
			
			return $result;

	    }

	    /**
	     * Description
	     * @param string $client_id Nest Client ID
	     * @param string $client_secret Nest Shared secret
	     * @param string $code Nest temporary code for getting auth token
	     * @return string OAuth token that comes back from Nest.
	     */
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

	    /**
	     * Sets the access token for use with the Nest account. 
	     * @param string $access_token OAuth token that comes back from Nest.
	     * @return null
	     */
	    function setAccessToken($access_token) {
	    	$this->access_token = $access_token;
	    }

	    /**
	     * Tests whether or not the string can be converted into JSON. 
	     * @param string $str 
	     * @return boolean
	     */
	    private function canBeJSON($str) {
	    	$json_array = json_decode( trim($str) , true );

			if( $json_array == NULL ) {
	    		return false;
	    	} else {
	    		return true;
	    	}
	    }
	    
	    /**
	     * Probably a poor test of whether or not a string is an object in json. 
	     * @param string $str 
	     * @return boolean
	     */
	    private function isJSONObject($str) {

	    	if (($str[0] == "{") && $this->canBeJSON($str)){
	    		return true;
	    	} else{
	    		return false;
	    	}
	    }

	    /**
	     * Parses $http_header_response arrays into a associative array for easier use. 
	     * @param  $http_header_response 
	     * @return array keyed for easier usage.
	     */
	    private function processHeaders($http_header_response){
	    	$results = array();
	    	$results['status_code'] = trim(explode(" ", $http_header_response[0])[1]);

	    	for ($i=1; $i< count($http_header_response); $i++){
	    		list($key,$array) = explode(":", $http_header_response[$i]);
	    		if ($key=="Location") {
	    			$results[strtolower($key)] = explode(" ", $http_header_response[$i])[1];
	    		} else{
	    			$results[strtolower($key)] = trim($array);
	    		}
	    	}

	    	return $results;
	    }	

	}	



?>
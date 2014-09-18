<?php
	$content = file_get_contents("sample.json");
	$data = json_decode($content);



	$path = array_slice(explode("/", $_GET['path']),1);

	if ( strlen(end($path)) == 0 ){
		array_pop($path);
	}

	if (count($path) == 0 ){
		$result  = $content;
	} else if (count($path) == 1 ){
		$result  = $data->$path[0];
	} else if (count($path) == 2 ){
		$result  = $data->$path[0]->$path[1];
	} else if (count($path) == 3 ){
		$result  = $data->$path[0]->$path[1]->$path[2];
	} else if (count($path) == 4 ){
		$result  = $data->$path[0]->$path[1]->$path[2]->$path[3];
	} 


	//TODO Find out http status codes for successes and failures.
	echo(json_encode($result));

?>
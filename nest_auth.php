<?php
	require 'config/settings.php';
	require 'lib/Nest/nest.php';
	$nest = new Nest();
	$access_token = $nest->getAuthToken($client_id[1], $client_secret[1], $_GET['code']);
    setcookie("nest_token", $access_token);
    header("Location: http://" . $_SERVER['HTTP_HOST']. "/index.php");
    die();
    

?>

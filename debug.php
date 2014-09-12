<?php
$filecontent = "REQUEST\n";
foreach (getallheaders() as $name => $value) {
    $filecontent .= "$name: $value\n";
}



$putdata = fopen("php://input", "r");
$str = stream_get_contents($putdata);
fclose($putdata);


$filecontent .= "PUT\n";
$filecontent .= $str;

$filename = time() . ".log";


	file_put_contents("logs/" . $filename, $filecontent);
	




?>
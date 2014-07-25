<?php

include 'sites.php';

$hosts = array();
foreach($sites as $site) {
	$hosts[] = $site["domain"];
}

// Verify requested site before loading anything
if(empty($_REQUEST['host']) || !in_array($_REQUEST['host'], $hosts)) {
	header("Content-type: application/json");
	die('{error: 2}');
}

$host = $_REQUEST['host'];
try {
	$result = file_get_contents("http://" . $host . "/?json=1");
	$data = json_decode($result);
} catch(Exception $e) {
	$data = new StdClass();
	$data->error = 1;
}
$data->id = sha1($host);
$data->domain = $host;

header("Content-type: application/json");
echo json_encode($data);

<?php
require 'config.php';
$db = mysqli_connect($host, $user, $password, $database);

if ($db->connect_errno !== 0) {
    if (isset($errorAsJSON) && $errorAsJSON)
	exit('{"error" : true, "desc" : "could not connect to database"}');
    exit("could not connect to databse");
}

function getPassed($name) {
    return $_GET && isset($_GET[$name]);
}

function postPassed($name) {
    return $_POST && isset($_POST[$name]);
}
?>

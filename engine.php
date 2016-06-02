<?php
require 'config.php';
$db = mysqli_connect($host, $user, $password, $database);
/*
 * if ($db->connect_errno !== 0) {
 *     exit('{"result" : "could not connect to database"}');
 * }
 * */
function getPassed($name) {
    return $_GET && isset($_GET[$name]);
}
?>

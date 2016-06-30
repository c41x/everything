<?php
$charsetPostfix = ' CHARACTER SET utf8 COLLATE utf8_bin ';
$host = 'localhost';
$database = 'everything';
$user = 'root';
$password = 'pass';
$uploadDir = 'resources/';

function getPassed($name) {
    return $_GET && isset($_GET[$name]);
}

function postPassed($name) {
    return $_POST && isset($_POST[$name]);
}
?>

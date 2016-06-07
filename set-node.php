<?php
$errorAsJSON = true;
require 'engine.php';

if (getPassed('id')) {
    $id = $_GET['id'];
}

echo 'saved';
?>

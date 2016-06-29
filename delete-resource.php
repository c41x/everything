<?php
$errorAsJSON = true;
require 'engine.php';

if (postPassed('id')) {
    if (unlink($uploadDir.'/'.$_POST['id']) == FALSE) {
	exit(json_encode(array('error' => TRUE, 'desc' => 'resource could not be deleted')));
    }

    exit(json_encode(array('error' => FALSE)));
}

echo json_encode(array('error' => TRUE, 'desc' => 'ID not passed / invalid'));
?>

<?php
require 'engine.php';

if (getPassed('id')) {
    $id = $_GET['id'];
    if (is_numeric($id)) {
	$deleteResult = $db->query('DELETE FROM things WHERE id="'.$id.'"');
	if ($deleteResult !== FALSE) {
	    exit('successfully deleted thing');
	}
	else {
	    exit("query returned false");
	}
    }
    else {
	exit("passed id is not integer");
    }
}

echo "id parameter not specified";
?>

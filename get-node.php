<?php
$errorAsJSON = true;
require 'engine.php';

if (getPassed('id')) {
    $id = $_GET['id'];
    if (is_numeric($id)) {
	$selectResult = $db->query('SELECT state FROM nodes WHERE id="'.$id.'"');
	if ($selectResult !== FALSE && $selectResult->num_rows == 1 && $row = $selectResult->fetch_assoc()) {
	    exit(json_encode(array('error' => FALSE, 'state' => $row['state'])));
	}
	else {
	    exit('{"error" : true, "desc" : "query returned false"}');
	}
    }
    else {
	exit('{"error" : true, "desc" : "passed id is not integer"}');
    }
}

exit('{"error" : true, "desc" : "id parameter not specified"}');
?>

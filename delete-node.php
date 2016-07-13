<?php
$errorAsJSON = true;
require 'engine.php';

if (getPassed('id')) {
    $id = $_GET['id'];
    if (is_numeric($id)) {
	$deleteResult = $db->query('DELETE FROM nodes WHERE id="'.$id.'"');
	if ($deleteResult !== FALSE) {
	    exit(json_encode(array('error' => FALSE, 'desc' => 'successfully deleted node')));
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

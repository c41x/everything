<?php
$errorAsJSON = true;
require 'engine.php';

if (getPassed('id') && postPassed('state')) {
    $id = $_GET['id'];
    $state = mysqli_escape_string($db, $_POST['state']);
    if (is_numeric($id)) {
	$updateResult = $db->query('UPDATE nodes SET state="'.
				   $state
				  .'" WHERE id='.$id);
	if ($updateResult !== FALSE) {
	    exit('{"error" : false, "desc" : "node updated"}');
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

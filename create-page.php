<?php
$errorAsJSON = true;
require 'engine.php';

if (postPassed('title')) {
    $pageResult = $db->query('INSERT INTO pages (title) VALUES('.
			    '"'.mysqli_escape_string($db, $_POST['title']).'")');
    if ($pageResult !== FALSE) {
	$iid = $db->insert_id;
	$data = array('id' => $iid);
	echo json_encode($data);
    }
    else {
	exit('{"error" : true, "desc" : "error while inserting page"}');
    }
}
else {
    exit('{"error" : true, "desc" : "title not specified"}');
}

?>

<?php
$errorAsJSON = true;
require 'engine.php';

if (postPassed('title')) {
    // return existing page
    $pageResult = $db->query('SELECT * FROM pages WHERE title="'.
			     mysqli_escape_string($db, $_POST['title']).'" LIMIT 1');
    if ($pageResult !== FALSE && $pageResult->num_rows == 1 && $row = $pageResult->fetch_assoc()) {
	exit(json_encode(array('error' => FALSE, 'id' => $row['id'])));
    }

    // insert new one
    $pageResult = $db->query('INSERT INTO pages (title) VALUES('.
			    '"'.mysqli_escape_string($db, $_POST['title']).'")');
    if ($pageResult !== FALSE) {
	$iid = $db->insert_id;
	$data = array('error' => FALSE, 'id' => $iid);
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

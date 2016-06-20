<?php
$errorAsJSON = true;
require 'engine.php';

function processHTML(&$thing, $iid) {
    return str_replace('{id}', $thing['name_id'].$iid, $thing['html']);
}

function processID(&$thing, $iid) {
    return $thing['name_id'].$iid;
}

if (getPassed('type') && getPassed('page') && is_numeric($_GET['page'])) {
    $thingResult = $db->query('SELECT * FROM things WHERE name_id="'.
			      mysqli_escape_string($db, $_GET['type']).'"');
    if ($thingResult !== FALSE && $thingResult->num_rows > 0) {
	$thing = $thingResult->fetch_assoc();
	$nodeResult = $db->query('INSERT INTO nodes (id_things, id_page, state) VALUES('.
				 $thing['id'].', '.
				$_GET['page'].',"")');
	if ($nodeResult !== FALSE) {
	    $iid = $db->insert_id;
	    $data = array('html' => processHTML($thing, $iid),
			  'id' => processID($thing, $iid));
	    echo json_encode($data);
	}
	else {
	    exit('{"error" : true, "desc" : "error while inserting node"}');
	}
    }
    else {
	exit('{"error" : true, "desc" : "thing not found in database"}');
    }
}
else {
    exit('{"error" : true, "desc" : "type / page not specified"}');
}

?>

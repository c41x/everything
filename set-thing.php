<?php
require 'engine.php';

if (getPassed('id') && postPassed('name_id') && postPassed('pretty_name') &&
    postPassed('html') && postPassed('js') && postPassed('css')) {
    if ($db->query('UPDATE things SET '.
		   'name_id=\''.mysqli_escape_string($db, $_POST['name_id']).'\', '.
		   'pretty_name=\''.mysqli_escape_string($db, $_POST['pretty_name']).'\', '.
		   'html=\''.mysqli_escape_string($db, $_POST['html']).'\', '.
		   'js=\''.mysqli_escape_string($db, $_POST['js']).'\', '.
		   'css=\''.mysqli_escape_string($db, $_POST['css']).'\''.
		   ' WHERE id='.$_GET['id']) === FALSE) {
	exit('{"error" : true, "desc" : "update query failed"}');
    }
    else {
	exit('{"error" : false, "desc" : "thing updated"}');
    }
}
else {
    exit('{"error" : true, "desc" : "could not update thing: POST data incomplete"}');
}
?>

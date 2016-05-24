<?php

function addLine($s) {
    echo $s.'<br />';
}

function tableExists($db, $tableName) {
    return $db->query('SELECT 1 FROM `'.$tableName.'` LIMIT 1') !== FALSE;
}

$db = mysqli_connect("localhost", "root", "pass", "everything");

if ($db->connect_errno) {
    addLine("could not connect to server.");
}
else {
    addLine("connection to MySQL database established.");

    if (tableExists($db, 'things')) {
	addLine("table things - ok");
	// TODO: verify?
    }
    else {
	addLine("creating things table...");
	if ($db->query("CREATE TABLE things (".
		       "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,".
		       "name_id VARCHAR(255) NOT NULL,".
		       "pretty_name VARCHAR(255) NOT NULL,".
		       "html TEXT,".
		       "js TEXT,".
		       "css TEXT".
		       ")") == FALSE) {
	    addLine("error creating things table");
	}
	else {
	    addLine("table things created");
	}
    }
}

?>

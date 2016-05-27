<?php

function addLine($s) {
    echo $s.'<br />';
}

function tableExists($db, $tableName) {
    return $db->query('SELECT 1 FROM `'.$tableName.'` LIMIT 1') !== FALSE;
}

$charsetPostfix = ' CHARACTER SET utf8 COLLATE utf8_bin ';
$db = mysqli_connect("localhost", "root", "pass", "everything");

if ($db->connect_errno) {
    addLine("could not connect to server.");
}
else {
    addLine("connection to MySQL database established.");

    // things
    if (tableExists($db, 'things')) {
	addLine("table things - ok");
	// TODO: verify?
    }
    else {
	addLine("creating things table...");
	if ($db->query("CREATE TABLE things (".
		       "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,".
		       "name_id VARCHAR(255)".$charsetPostfix."NOT NULL,".
		       "pretty_name VARCHAR(255)".$charsetPostfix."NOT NULL,".
		       "html TEXT".$charsetPostfix.",".
		       "js TEXT".$charsetPostfix.",".
		       "css TEXT".$charsetPostfix.
		       ")") == FALSE) {
	    addLine("error creating things table");
	}
	else {
	    addLine("table things created");
	}
    }

    // nodes
    if (tableExists($db, 'nodes')) {
	addLine("table nodes - ok");
    }
    else {
	addLine("creating nodes table...");
	if ($db->query("CREATE TABLE nodes (".
		       "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,".
		       "id_things INT UNSIGNED NOT NULL, ".
		       "state TEXT".$charsetPostfix
		       ")") == FALSE) {
	    addLine("error creating things nodes");
	}
	else {
	    addLine("table nodes created");
	}
    }

    // pages
}

?>

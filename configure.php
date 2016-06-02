<?php
require 'engine.php';

function addLine($s) {
    echo $s.'<br />';
}

function tableExists($db, $tableName) {
    return $db->query('SELECT 1 FROM `'.$tableName.'` LIMIT 1') !== FALSE;
}

function initializeDatabse($db) {
    // things
    if (tableExists($db, 'things')) {
	addLine('table things - ok');
    }
    else {
	addLine('creating things table...');
	if ($db->query('CREATE TABLE things ('.
		       'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,'.
		       'name_id VARCHAR(255)'.$charsetPostfix.'NOT NULL,'.
		       'pretty_name VARCHAR(255)'.$charsetPostfix.'NOT NULL,'.
		       'html TEXT'.$charsetPostfix.','.
		       'js TEXT'.$charsetPostfix.','.
		       'css TEXT'.$charsetPostfix.
		       ')') == FALSE) {
	    addLine('error creating things table');
	}
	else {
	    addLine('table things created');
	}
    }

    // nodes
    if (tableExists($db, 'nodes')) {
	addLine('table nodes - ok');
    }
    else {
	addLine('creating nodes table...');
	if ($db->query('CREATE TABLE nodes ('.
		       'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,'.
		       'id_things INT UNSIGNED NOT NULL, '.
		       'state TEXT'.$charsetPostfix.
		       ')') == FALSE) {
	    addLine('error creating things nodes');
	}
	else {
	    addLine('table nodes created');
	}
    }

    // pages
    if (tableExists($db, 'pages')) {
	addLine('table pages - ok');
    }
    else {
	addLine('creating pages table...');
	if ($db->query('CREATE TABLE pages ('.
		       'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, '.
		       'title VARCHAR(255)'.$charsetPostfix.
		       ')') == FALSE) {
	    addLine('error creating pages table');
	}
	else {
	    addLine('table pages created');
	}
    }

    // page_nodes
    if (tableExists($db, 'page_nodes')) {
	addLine('table page_nodes - ok');
    }
    else {
	addLine('creating page_nodes table...');
	if ($db->query('CREATE TABLE page_nodes ('.
		       'id_page INT UNSIGNED NOT NULL, '.
		       'id_node INT UNSIGNED NOT NULL'.
		       ')') == FALSE) {
	    addLine('error creating page_nodes table');
	}
	else {
	    addLine('table page_nodes created');
	}
    }
}

if ($db->connect_errno !== 0) {
    addLine('could not connect to server');
}
else {
    addLine('connection to MySQL database established.');
    if ($_GET) {
	if (isset($_GET['install']) && $_POST && isset($_POST['name_id']) && isset($_POST['pretty_name']) &&
	    isset($_POST['html']) && isset($_POST['js']) && isset($_POST['css'])) {
	    if ($db->query('INSERT INTO things (name_id, pretty_name, html, js, css) VALUES ('.
			   '\''.mysqli_escape_string($db, $_POST['name_id']).'\', '.
			   '\''.mysqli_escape_string($db, $_POST['pretty_name']).'\', '.
			   '\''.mysqli_escape_string($db, $_POST['html']).'\', '.
			   '\''.mysqli_escape_string($db, $_POST['js']).'\', '.
			   '\''.mysqli_escape_string($db, $_POST['css']).'\''.
			   ')') == FALSE) {
		addLine('could not install thing');
	    }
	    else {
		addLine('thing "'.$_POST['pretty_name'].'" installed');
	    }
	}
	else {
	    addLine('could not install thing: POST data incomplete');
	}
    }
    else {
	initializeDatabse($db);
    }
    addLine('done');
}

$db->close();

// TODO: verify tables structure
?>

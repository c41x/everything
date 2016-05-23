<?php

function addLine($s) {
	echo $s.'<br />';
}

function tableExists($tableName) {
	return mysqli_query('select 1 from `'.mysqli_real_escape_string($tableName).'` limit 1') !== FALSE;
}

if (mysqli_connect("localhost", "root", "pass", "everything") == FALSE) {
	addLine("could not connect to server.");
}
else {
	addLine("connection to MySQL database established.");
	// TODO: check for tables, if there are none - query create one
}

?>
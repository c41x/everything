<?php
require 'engine.php';

$pages = array();

if (getPassed('term')) {
    $pagesResult = $db->query('SELECT * FROM pages WHERE LOWER(title) LIKE LOWER("%'.
			      mysqli_escape_string($db, $_GET['term']).'%") LIMIT 30');
    error_log('SELECT * FROM pages WHERE LOWER(title) LIKE LOWER("'.
	      mysqli_escape_string($db, $_GET['term']).'") LIMIT 30');
    if ($pagesResult !== FALSE && $pagesResult->num_rows > 0) {
	while ($row = $pagesResult->fetch_assoc()) {
	    array_push($pages, $row['title']);
	}
    }
}

echo json_encode($pages);

?>

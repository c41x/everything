<?php
session_start();
require 'engine.php';

// get all things
$thingsResult = $db->query('SELECT * FROM things');
$things = array();
if ($thingsResult !== FALSE && $thingsResult->num_rows > 0) {
    while ($row = $thingsResult->fetch_assoc()) {
	$things[$row['id']] = $row;
    }
}

// detect current page
$pageID = 1;
if (getPassed('id') && is_numeric($_GET['id'])) {
    $pageID = $_GET['id'];
}

// get current page info
$pageResult = $db->query('SELECT * FROM pages WHERE id='.$pageID);
$page = NULL;
if ($pageResult !== FALSE && $pageResult->num_rows > 0) {
    while ($row = $pageResult->fetch_assoc()) {
	$page = $row;
    }
}

// if page not found in database -> return empty page
if ($page === NULL) {
    die("Wrong page ID passed");
}

// remember current page
if (!isset($_SESSION['path']))
    $_SESSION['path'] = array();
if ((!empty($_SESSION['path']) && end($_SESSION['path'])[0] !== $pageID) || empty($_SESSION['path']))
    array_push($_SESSION['path'], array($pageID, $page['title']));

// load page nodes
$nodesResult = $db->query('SELECT * FROM nodes WHERE id_page='.$pageID);
$nodes = array();
if ($nodesResult !== FALSE && $nodesResult->num_rows > 0) {
    while ($row = $nodesResult->fetch_assoc()) {
	$nodes[$row['id']] = $row;
    }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<head>
<title>Everything</title>

<style>
 body { margin: 0; background: #eeeeee; }
 .ui-button-text { font-size: .7em; }
 .ui-progressbar, toolbar {
   position: fixed;
 }
 <?php
 foreach ($things as &$thing) {
     echo $thing['css'];
 }
 ?>
</style>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="external/content-tools.min.css">
<link rel="stylesheet" type="text/css" href="external/content-tools-alignment.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="external/content-tools.min.js"></script>
</head>
<body>
  <script>
   $(function() {
       var toID = function(selector) {
	   return selector.match(/(\d+)/)[1];
       }

       var genericSpawn = function(type, setupFunction, serializeFunction) {
	   $.ajax({
	       dataType: "json",
	       url: "create-node.php?type=" + type + "&page=" + <?php echo $pageID; ?>,
	       success: function(data) {
		   if (!data.error) {
		       alert("spawning: " + data.html);
		       $(document.body).append(data.html);
		       setupFunction("#" + data.id);
		       serializeFunction("#" + data.id);
		   }
		   else {
		       alert("error creating node: " + data.desc);
		   }
	       },
	       error: function(a, b, c) {
		   alert(b);
		   alert(c);
	       }
	   });
       }

       var genericRemove = function(id) {
	   $.ajax({
	       dataType: "json",
	       url: "delete-node.php?id=" + id,
	       success: function(data) {
		   alert("deleted node " + id + " response: " + data.result);
		   $("#" + id).remove();
	       },
	       error: function(a, b, c) {
		   alert(b);
		   alert(c);
	       }
	   });
       };

       <?php
       // generate all js code
       foreach ($things as &$thing) {
	   echo $thing['js'];
	   echo 'var spawn'.$thing['name_id'].' = function() { genericSpawn("'.
		$thing['name_id'].'", setup'.$thing['name_id'].', serialize'.$thing['name_id'].'); };';
	   echo 'remove'.$thing['name_id'].' = function(id) { genericRemove(id); };';
	   echo '$("#btnCreate'.$thing['name_id'].'").button().click(spawn'.$thing['name_id'].');';
       }
       ?>

       var progressbar = $("#loadingbar");
       progressbar.progressbar({ value: 0 }).height(10);

       var loaded = 0;
       var onNodeDeserialized = function() {
	   loaded++;
	   var total = <?php echo count($nodes); ?>;
	   if (loaded == total)
	       progressbar.hide();
	   else progressbar.progressbar("value", (loaded / total) * 100);
       };

       <?php
       foreach ($nodes as &$node) {
	   $myThing = $things[$node['id_things']];
	   echo 'setup'.$myThing['name_id'].'("#'.$myThing['name_id'].$node['id'].'");';
	   echo 'deserialize'.$myThing['name_id'].'("#'.$myThing['name_id'].$node['id'].'");';
       }
       ?>

       // create some page / test
       $("#createpage").click(function() {
	   $.ajax({
	       url: "create-page.php",
	       method: "POST",
	       dataType: "json",
	       data: {title: "Granite Engine"},
	       success: function(data) {
		   if (data.error) alert(data.desc);
		   else alert("created page with ID = " + data.id);
	       },
	       error: function(a, b, c) {
		   alert(b);
		   alert(c);
	       }
	   });
       });

       //removeDraggable("n1");
   });
  </script>
  <div id="toolbar" class="ui-widget-header ui-corner-all">
  <?php
  foreach ($things as &$thing) {
      echo '<button id="btnCreate'.$thing['name_id'].'">'.$thing['name_id'].'</button>';
  }
  ?>
  </div>
  <div id="loadingbar"></div>
  <?php
  foreach ($nodes as &$node) {
      $myThing = $things[$node['id_things']];
      echo str_replace('{id}', $myThing['name_id'].$node['id'], $myThing['html']);
  }
  ?>
  <!-- >p><?php echo print_r($_SESSION['path']); ?></p-->
  <p id="createpage">Create page</p>
</body></html>
<?php
$db->close();
// TODO: links (breadcrumbs)
// TODO: resources
// TODO: admin panel
// TODO: mod_rewrite
// TODO: removing
?>

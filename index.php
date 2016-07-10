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
<title>Everything - <?php echo $page['title']; ?></title>

<style>
 body { margin: 0; background: #eeeeee; }
 .ui-button-text { font-size: .7em; }
 .ui-progressbar, toolbar {
   position: fixed;
 }

 .breadcrumbs {
   margin: 0;
   padding: 0;
   list-style: none;
   position: fixed;
   bottom: 0px;
 }

 #breadcrumbs {
   background: #eee;
   border-width: 0px;
   box-shadow: 0 0 2px rgba(0,0,0,.2);
   overflow: hidden;
 }

 #breadcrumbs li {
   float: left;
 }

 #breadcrumbs a {
   padding: .2em 1em .2em 2em;
   float: left;
   text-decoration: none;
   color: #444;
   position: relative;
   text-shadow: 0 1px 0 rgba(255,255,255,.5);
   background-color: #ddd;
 }

 #breadcrumbs li:first-child a {
   padding-left: 1em;
   border-radius: 5px 0 0 5px;
 }

 #breadcrumbs a:hover {
   background: #fff;
 }

 #breadcrumbs a::after,
 #breadcrumbs a::before {
   content: "";
   position: absolute;
   top: 50%;
   margin-top: -1.5em;
   border-top: 1.5em solid transparent;
   border-bottom: 1.5em solid transparent;
   border-left: 1em solid;
   right: -1em;
 }

 #breadcrumbs a::after {
   z-index: 2;
   border-left-color: #ddd;
 }

 #breadcrumbs a::before {
   border-left-color: #ccc;
   right: -1.1em;
   z-index: 1;
 }

 #breadcrumbs a:hover::after {
   border-left-color: #fff;
 }

 #breadcrumbs .current,
 #breadcrumbs .current:hover {
   font-weight: bold;
   background: none;
 }

 #breadcrumbs .current::after,
 #breadcrumbs .current::before {
   content: normal;
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
<script src="external/SimpleAjaxUploader.min.js"></script>
<script src="external/ace/ace.js" type="text/javascript" charset="utf-8"></script>
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
		       //alert("spawning: " + data.html);
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
  foreach ($things as &$thing) {
      echo $thing['static_html'];
  }

  foreach ($nodes as &$node) {
      $myThing = $things[$node['id_things']];
      echo str_replace('{id}', $myThing['name_id'].$node['id'], $myThing['html']);
  }
  ?>
  <ul class="breadcrumbs" id="breadcrumbs">
    <li><a href="index.php">&#127968;</a></li>
    <?php
    // limit to 5 elements
    while (count($_SESSION['path']) > 7)
	array_shift($_SESSION['path']);

    $count = count($_SESSION['path']);
    $i = 0;
    foreach ($_SESSION['path'] as &$path) {
	$class = '';
	if (++$i === $count)
	    $class = ' class="current"';
	echo '<li><a href="index.php?id='.$path[0].'"'.$class.'>'.$path[1].'</a></li>';
    }
    ?>
</ul>
</body></html>
<?php
$db->close();

// TODO: admin panel
// TODO: mod_rewrite
// TODO: removing
// TODO: file thing

?>

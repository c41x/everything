<?php
require 'engine.php';
$thingsResult = $db->query('SELECT * FROM things');
$things = array();
if ($thingsResult !== FALSE && $thingsResult->num_rows > 0) {
    while ($row = $thingsResult->fetch_assoc()) {
	$things[$row['id']] = $row;
    }
}

$nodesResult = $db->query('SELECT * FROM nodes');
$nodes = array();
if ($nodesResult !== FALSE && $nodesResult->num_rows > 0) {
    while ($row = $nodesResult->fetch_assoc()) {
	$nodes[$row['id']] = $row;
    }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<title>Everything</title>

<style>
 body { margin: 0; }
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
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<body>
  <script>
   $(function() {
       var toID = function(selector) {
	   return selector.match(/(\d+)/)[1];
       }

       var genericSpawn = function(type, setupFunction, serializeFunction) {
	   $.ajax({
	       dataType: "json",
	       url: "create-node.php?type=" + type,
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
</body>
<?php
$db->close();
?>

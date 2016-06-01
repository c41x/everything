<?php
require 'config.php';
$db = mysqli_connect($host, $user, $password, $database);
$thingsResult = $db->query('SELECT * FROM things');
$things = array();
$i = 0;
if ($thingsResult->num_rows > 0) {
    while ($row = $thingsResult->fetch_assoc()) {
	$things[$i++] = $row;
    }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<title>Everything</title>
<style>
 body { margin: 0; }
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
       var genericSpawn = function(type, setupFunction) {
	   $.ajax({
	       dataType: "json",
	       url: "create-node.php?type=" + type,
	       success: function(data) {
		   alert("spawning: " + data.html);
		   $(document.body).append(data.html);
		   setupFunction("#" + data.id);
	       },
	       error: function(a, b, c) {
		   alert(b);
		   alert(c);
	       }
	   });
       }

       <?php
       foreach ($things as &$thing) {
	   echo $thing['js'];
	   echo 'var spawn'.$thing['name_id'].' = function() { genericSpawn("'.
		$thing['name_id'].'", setup'.$thing['name_id'].'); }';
	   // TODO: remove from server
	   //echo 'remove'.$thing['name_id'].' = function(id) { $id.remove(); }';
       }
       ?>

       var removeDraggable = function(id) {
	   // 1) remove from DOM
	   // 2) send AJAX request to remove, print error if disconnected
	   $(id).remove();
       };

       // content loading
       // 1) initialize progress bar code, setup & run progressbar
       // 2) iterate through all nodes and call "deserialize" + <thing_name>
       // 3) close progress bar or display error message box

       // spawning new node
       $(document.body).keypress(function(){
	   spawnDraggable();
       });
   });
  </script>
  <!-- server inserts menu & controls -->
  <!-- server inserts html for all nodes here -->
</body>

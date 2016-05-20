<!DOCTYPE html>
<meta charset="utf-8">
<title>Everything</title>
<style>

 .draggable { width: 150px; height: 150px; padding: 0.5em; }
 body { margin: 0; }

</style>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<body>
  <script>
   $(function() {
       /*
	  user specified content all stored in DB
	  as JS part. all functions must follow naming
	  convention, all functions must work with class or id
	*/
       var serializeDraggable = function(id) {
	   // 1) gets state as JSON
	   // 2) sends update request to server, print error if disconnected
	   // or 2) push data to update queue?
	   var obj = $(id);
	   return [obj.offset().left, obj.offset().top];
       };

       var deserializeDraggable = function(id) {
	   // 1) sends request for state
	   // 2) apply state
	   $(id).offset({top : 200, left : 200});
       };

       var setupDraggable = function(id) {
	   // main JS logic on id - initialize all
	   $(id).draggable({
               drag: function() {
		   var offset = $(this).offset();
		   var xPos = offset.left;
		   var yPos = offset.top;
		   $('#posX').text('x: ' + xPos);
		   $('#posY').text('y: ' + yPos);
               },
               stop: function() {
		   // just tests
		   //alert(serializeDraggable(id));
		   //removeDraggable(id);
               }
	   });
       };

       /*
	  automatically generated for each thing type
	*/
       var spawnDraggable = function() {
	   // 1) name is generated from "spawn" + <thing_name>
	   // 2) sends AJAX request with thing id e.g.: create.php?thing=draggable
	   // 3) server returns JSON with html: "html code" and id: "#id"
	   // 4) code is calling "setup" + <thing_name> function for JS setup part
	   $(document.body).append('<div id="n1" class="ui-widget-content draggable"><p>Draggable node</p><p id = "posX">x</p><p id = "posY">y</p> </div>');
	   setupDraggable("#" + "n1");
       };

       var removeDraggable = function(id) {
	   // 1) remove from DOM
	   // 2) send AJAX request to remove, print error if disconnected
	   $(id).remove();
       };

       // temporary (result of "create draggable" button)
       spawnDraggable();

       // content loading
       // 1) initialize progress bar code, setup & run progressbar
       // 2) iterate through all nodes and call "deserialize" + <thing_name>
       // 3) close progress bar or display error message box
       deserializeDraggable("#n1");
   });
  </script>
  <!-- server inserts menu & controls -->
  <!-- server inserts html for all nodes here -->
</body>

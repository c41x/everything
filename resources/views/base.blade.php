<!DOCTYPE html>
<meta charset="utf-8">
<title>Everything</title>
<style>

 #n1 { width: 150px; height: 150px; padding: 0.5em; }
 body { margin: 0; }

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<body>
  <script>
   $(function() {
       // user specified
       var serializeAddOffset = function(obj) {
	   return [obj.offset().left, obj.offset().top];
       };

       var setupDraggable = function(id) {
	   $(id).draggable({
               drag: function() {
		   var offset = $(this).offset();
		   var xPos = offset.left;
		   var yPos = offset.top;
		   $('#posX').text('x: ' + xPos);
		   $('#posY').text('y: ' + yPos);
               },
	       stop: function() {
		   // update database
		   //alert(serializeAddOffset($(this)));
	       }
	   });
       };

       // automatically generated from DB
       var spawnDraggable = function() {
	   // change to AJAX request returnig id
	   $(document.body).append('<div id="n1" class="ui-widget-content"><p>Draggable node</p><p id = "posX">x</p><p id = "posY">y</p> </div>');
	   setupDraggable("#" + "n1");
       };

       // temporary (result of "create draggable" button)
       spawnDraggable();

       // generated automatically from nodes table
       $("#n1").offset({top: 200, left : 200});
   });
  </script>
</body>

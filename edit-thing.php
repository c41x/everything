<?php
require 'engine.php';
if (getPassed('id') && is_numeric($_GET['id'])) {
    $thingResult = $db->query('SELECT * FROM things WHERE id='.$_GET['id']);
    if ($thingResult !== FALSE && $thingResult->num_rows > 0) {
	$thing = $thingResult->fetch_assoc();
    }
    else {
	exit('query error: '.'SELECT * FROM things WHERE id='.$_GET['id']);
    }
}
else {
    exit('id GET parameter not specified / wrong format');
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Everything - Thing Editor</title>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <style>
     .ui-button { font-size: .7em; }
    </style>
    <script>
     $(function() {
	 $("input[type=submit]")
	     .button()
	     .click(function(event) {
		 event.preventDefault();

		 $.ajax({
		     url: $("#editForm").attr("action"),
		     type: 'POST',
		     dataType: "json",
		     data: $("#editForm").serialize(),
		     success: function(result) {
			 if (result.error) {
			     alert(result.desc);
			 }
			 else {
			     color = $("#submitButton").css("background");
			     $("#submitButton").css("background", "GreenYellow");
			     setTimeout(function() {
				 $("#submitButton").css("background", color);
			     }, 3000);
			 }
		     },
		     error: function(a, b, c) {
			 alert(a);
			 alert(b);
			 alert(c);
		     }
		 });
	     });
     });
    </script>
  </head>
  <body>
    <form id="editForm" action="set-thing.php?id=<?php echo $thing['id']; ?>" method="post">
      Name ID: <br /><input type="text" name="name_id" value="<?php echo $thing['name_id']; ?>"></textarea><br />
      Pretty Name: <br /><input type="text" name="pretty_name" value="<?php echo $thing['pretty_name']; ?>"></textarea><br />
      HTML: <br /><textarea rows="10" cols="100" name="html"><?php echo $thing['html']; ?></textarea><br />
      JS: <br /><textarea rows="10" cols="100" name="js"><?php echo $thing['js']; ?></textarea><br />
      CSS: <br /><textarea rows="10" cols="100" name="css"><?php echo $thing['css']; ?></textarea><br />
      <input id="submitButton" type="submit" value="Save">
    </form>
  </body>
</html>

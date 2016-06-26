<?php
require 'engine.php';
if (getPassed('id') && is_numeric($_GET['id'])) {
    $thingResult = $db->query('SELECT * FROM things WHERE id='.$_GET['id']);
    if ($thingResult !== FALSE && $thingResult->num_rows > 0) {
	$thing = $thingResult->fetch_assoc();
    }
    else {
	exit('query error: could not found id in database - '.$_GET['id']);
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
    <script src="external/ace/ace.js" type="text/javascript" charset="utf-8"></script>
    <style>
     .ui-button { font-size: .7em; }
     #editJs { width: 1000px; height: 500px; }
     #editCSS { width: 1000px; height: 200px; }
     #editHTML { width: 1000px; height: 300px; }
     #editStaticHTML { width: 1000px; height: 300px; }
     body { background: #2b2b2b; color: grey; margin: 50px; }
    </style>
    <script>
     $(function() {

	 var editJs = ace.edit("editJs");
	 editJs.setTheme("ace/theme/monokai");
	 editJs.getSession().setMode("ace/mode/javascript");

	 var editHTML = ace.edit("editHTML");
	 editHTML.setTheme("ace/theme/monokai");
	 editHTML.getSession().setMode("ace/mode/html");

	 var editStaticHTML = ace.edit("editStaticHTML");
	 editStaticHTML.setTheme("ace/theme/monokai");
	 editStaticHTML.getSession().setMode("ace/mode/html");

	 var editCSS = ace.edit("editCSS");
	 editCSS.setTheme("ace/theme/monokai");
	 editCSS.getSession().setMode("ace/mode/css");

	 $("input[type=submit]")
	     .button()
	     .click(function(event) {
		 event.preventDefault();

		 $.ajax({
		     url: $("#editForm").attr("action"),
		     type: 'POST',
		     dataType: "json",
		     data: {name_id: $("#name_id").val(),
			    pretty_name: $("#pretty_name").val(),
			    js: editJs.getValue(),
			    html: editHTML.getValue(),
			    static_html: editStaticHTML.getValue(),
			    css: editCSS.getValue()},
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
      Name ID: <br /><input type="text" name="name_id" id="name_id" value="<?php echo $thing['name_id']; ?>"></textarea><br />
      Pretty Name: <br /><input type="text" name="pretty_name" id="pretty_name" value="<?php echo $thing['pretty_name']; ?>"></textarea><br /><br />
      <input id="submitButton" type="submit" value="Save"><br /><br />
      HTML: <br /><div id="editHTML"><?php echo htmlspecialchars($thing['html']); ?></div><br />
      Static HTML: <br /><div id="editStaticHTML"><?php echo htmlspecialchars($thing['static_html']); ?></div><br />
      JS: <br /><div id="editJs"><?php echo htmlspecialchars($thing['js']); ?></div><br />
      CSS: <br /><div id="editCSS"><?php echo htmlspecialchars($thing['css']); ?></div>
    </form>
  </body>
</html>

<!DOCTYPE html>
<meta charset="utf-8">

<head>
  <title>Content Edit Test</title>
  <link rel="stylesheet" type="text/css" href="external/content-tools.min.css">
  <link rel="stylesheet" type="text/css" href="external/content-tools-alignment.css">
</head>

<body>
  <div data-editable data-name="main-content">
    <blockquote>
      Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live.
    </blockquote>
    <p>John F. Woods</p>
  </div>

  <div data-editable data-name="draggable3">
    <p>JJ</p>
  </div>

  <script src="external/content-tools.min.js"></script>
  <script>
   var editor = ContentTools.EditorApp.get();
   editor.init('*[data-editable]', 'data-name');
   editor.addEventListener('saved', function (ev) {
       var name, payload, regions, xhr;

       // Check that something changed
       regions = ev.detail().regions;
       if (Object.keys(regions).length == 0) {
           return;
       }

       // Set the editor as busy while we save our changes
       this.busy(true);

       // Collect the contents of each region into a FormData instance
       payload = new FormData();
       for (name in regions) {
           if (regions.hasOwnProperty(name)) {
	       alert(name + " / " + regions[name]);
               payload.append(name, regions[name]);
           }
       }

       // Send the update content to the server to be saved
       function onStateChange(ev) {
           // Check if the request is finished
           if (ev.target.readyState == 4) {
               editor.busy(false);
               if (ev.target.status == '200') {
                   // Save was successful, notify the user with a flash
                   new ContentTools.FlashUI('ok');
               } else {
                   // Save failed, notify the user with a flash
                   new ContentTools.FlashUI('no');
               }
           }
       };
   });

  </script>
</body>
</html>

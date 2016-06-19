// user specified content all stored in DB
// as JS part. all functions must follow naming
// convention

var serializeDraggable = function(id) {
    // 1) gets state as JSON
    // 2) sends update request to server, print error if disconnected
    // or 2) push data to update queue?
    var obj = $(id);
    var dataToSend = JSON.stringify({"top" : obj.offset().top,
				     "left" : obj.offset().left});
    $.ajax({
	type: "POST",
	url: "set-node.php?id=" + toID(id),
	data: {state: dataToSend},
	dataType: "json",
	success: function(data) {
            if (data.error) alert(data.desc);
	    //else alert("data send to server: " + dataToSend + " response: " + data.desc);
	},
	error: function(a, b, c) {
	    alert("sending error: " + dataToSend);
	    alert(b);
	    alert(c);
	}
    });
};

var deserializeDraggable = function(id) {
    // 1) sends request for state
    // 2) apply state
    $.ajax({
	dataType: "json",
	url: "get-node.php?id=" + toID(id),
	success: function(data) {
            if (data.error) alert(data.desc);
            else {
		$(id).offset($.parseJSON(data.state));
		///alert("loaded from server: " + JSON.stringify(data));
                onNodeDeserialized();
            }
	},
	error: function(a, b, c) {
	    alert(b);
	    alert(c);
	}
    });
};

var setupDraggable = function(id) {
    // main JS logic on id - initialize all
    $(id).draggable({
        drag: function() {
	    var offset = $(this).offset();
	    var xPos = offset.left;
	    var yPos = offset.top;
	    $('.posX').text('x: ' + xPos);
	    $('.posY').text('y: ' + yPos);
        },
        stop: function() {
	    // just tests
	    serializeDraggable(id);
	    //removeDraggable(id);
        }
    });
};

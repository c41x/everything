var serializeImage = function(id) {
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

var deserializeImage = function(id) {
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

var setupImage = function(id) {
    $(id).draggable({stop: function() {
	    serializeImage(id);
        }
    });
};

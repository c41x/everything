var serializeCodeBox = function(id) {
    var obj = $(id);
    var editableObj = ace.edit(obj.attr("id") + "Editor");
    var dataToSend = JSON.stringify({"top" : obj.offset().top,
				     "left" : obj.offset().left,
				     "width" : obj.width(),
				     "height" : obj.height(),
				     "content" : editableObj.getValue(),
				     "mode": $(id + "Mode").val()});
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

var deserializeCodeBox = function(id) {
    $.ajax({
	dataType: "json",
	url: "get-node.php?id=" + toID(id),
	success: function(data) {
	    if (data.error) alert(data.desc);
	    else {
		var state = $.parseJSON(data.state);
		$(id).offset(state);
		$(id).width(state.width);
		$(id).height(state.height);
		var editor = ace.edit($(id).attr("id") + "Editor");
		editor.setValue(state.content);
		editor.getSession().setMode("ace/mode/" + state.mode);
		///alert("loaded from server: " + JSON.stringify(data));
		onNodeDeserialized();
	    }
	    loadedItems++;
	},
	error: function(a, b, c) {
	    alert(b);
	    alert(c);
	}
    });
};

var setupCodeBox = function(id) {
    $(id).draggable({
	stop: function() {
	    serializeCodeBox(id);
	}
    }).resizable({
	stop: function() {
	    serializeCodeBox(id);
	}
    });

    $(id + "Submit").click(function() {
	serializeCodeBox(id);
    });

    $(id + "Mode").change(function() {
	var editor = ace.edit($(id).attr("id") + "Editor");
	editor.getSession().setMode("ace/mode/" + $(this).val());
	serializeCodeBox(id);
    });

    var editor = ace.edit($(id).attr("id") + "Editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/javascript");
};

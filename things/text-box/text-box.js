var serializeTextBox = function(id) {
    var obj = $(id);
    var editableObj = $(id+"Editable");
    var dataToSend = JSON.stringify({"top" : obj.offset().top,
				     "left" : obj.offset().left,
                                     "width" : obj.width(),
                                     "height" : obj.height(),
                                     "html" : editableObj.html()});
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

var loadedItems = 0;
var deserializeTextBox = function(id) {
    // 1) sends request for state
    // 2) apply state
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
                $(id + "Editable").html(state.html);
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

var setupTextBox = function(id) {
    $(id).draggable({
	stop: function() {
	    serializeTextBox(id);
        }
    }).resizable({
        stop: function() {
            serializeTextBox(id);
        }
    });

    var editor = ContentTools.EditorApp.get();
    editor.init('*[data-editable]', 'data-name');
};

var editor = ContentTools.EditorApp.get();
editor.init('*[data-editable]', 'data-name');
editor.addEventListener('saved', function (ev) {
    var name, regions;

    // check that something changed
    regions = ev.detail().regions;
    if (Object.keys(regions).length == 0) {
        return;
    }

    // set the editor as busy while we save our changes
    this.busy(true);

    // serialize all changed items
    loadedItems = 0;
    for (name in regions) {
        if (regions.hasOwnProperty(name)) {
            //alert(name + " / " + regions[name]);
            serializeTextBox("#" + name);
        }
    }

    // wait for all requests to complete
    setInterval(function() { if (loadedItems == 0) editor.busy(false); }, 500);
});

editor.addEventListener('start', function (ev) {
    $(".text-box").draggable('disable');
});


editor.addEventListener('stop', function (ev) {
    $(".text-box").draggable('enable');
});

var serializeTextBox = function(id) {
    var obj = $(id);
    var editableObj = $(id+"Editable");
    return JSON.stringify({"top" : obj.offset().top,
			   "left" : obj.offset().left,
			   "width" : obj.width(),
			   "height" : obj.height(),
			   "html" : editableObj.html()});
};

var loadedItems = 0;
var deserializeTextBox = function(id, data) {
    if (!data.error) {
	var state = $.parseJSON(data.state);
	$(id).offset(state);
	$(id).width(state.width);
	$(id).height(state.height);
	$(id + "Editable").html(state.html);
    }
    loadedItems++;
};

var setupTextBox = function(id) {
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
	    saveTextBox(id);
	}
    }).resizable({
	stop: function() {
	    saveTextBox(id);
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
	    saveTextBox("#" + name);
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

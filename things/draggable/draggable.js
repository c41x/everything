var serializeDraggable = function(id) {
    // 1) gets state as JSON
    // 2) sends update request to server, print error if disconnected
    // or 2) push data to update queue?
    var obj = $(id);
    return JSON.stringify({"top" : obj.offset().top,
			   "left" : obj.offset().left});
};

var deserializeDraggable = function(id, data) {
    if (!data.error) {
	$(id).offset($.parseJSON(data.state));
    }
};

var deleteDraggable = function(id) {
}

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
	    saveDraggable(id);
	}
    });
};

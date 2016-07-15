var serializeCodeBox = function(id) {
    var obj = $(id);
    var editableObj = ace.edit(obj.attr("id") + "Editor");
    return JSON.stringify({"top" : obj.offset().top,
			   "left" : obj.offset().left,
			   "width" : obj.width(),
			   "height" : obj.height(),
			   "content" : editableObj.getValue(),
			   "mode": $(id + "Mode").val()
			  });
};

var deserializeCodeBox = function(id, data) {
    if (!data.error) {
	var state = $.parseJSON(data.state);
	$(id).offset(state);
	$(id).width(state.width);
	$(id).height(state.height);
	var editor = ace.edit($(id).attr("id") + "Editor");
	editor.setValue(state.content, 1);
	editor.getSession().setMode("ace/mode/" + state.mode);
	$(id + "Mode").val(state.mode);
    }
};

var setupCodeBox = function(id) {
    $(id).draggable({
	stop: function() {
	    saveCodeBox(id);
	}
    }).resizable({
	stop: function() {
	    saveCodeBox(id);
	}
    });

    $(id + "Submit").click(function() {
	saveCodeBox(id);
    });

    $(id + "Mode").change(function() {
	var editor = ace.edit($(id).attr("id") + "Editor");
	editor.getSession().setMode("ace/mode/" + $(this).val());
	saveCodeBox(id);
    });

    var editor = ace.edit($(id).attr("id") + "Editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/javascript");
};

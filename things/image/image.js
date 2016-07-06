function escapeTags( str ) {
    return String( str )
	.replace( /&/g, '&' )
	.replace( /"/g, '"' )
	.replace( /'/g, '&#39;' )
	.replace( /</g, '<' )
	.replace( />/g, '>' );
}

var initUploader = function(progressID, messageID, onSubmit, onSuccess) {
    var progressBar = $(progressID),
	msgBox = $(messageID);

    var uploader = new ss.SimpleUpload({
	button: "#imageUploadButton",
	dropzone: "#imageUploadDropZone",
	url: 'create-resource.php',
	name: 'uploadfile',
	multipart: true,
	hoverClass: 'hover',
	focusClass: 'focus',
	responseType: 'json',
	startXHR: function() {
	    progressBar.css("display", "block");
	    this.setProgressBar(progressBar);
	},
	onSubmit: function() {
	    msgBox.html('Uploading...');
	    onSubmit();
	},
	onComplete: function(filename, response) {
	    progressBar.css("display", "none");

	    if (!response) {
		msgBox.html('Unable to upload file');
		return;
	    }

	    if (response.error === false) {
		onSuccess(response.id); // pass filename
		msgBox.html('');
	    }
	    else {
		if (response.desc) {
		    msgBox.html(escapeTags(response.desc));
		}
		else {
		    msgBox.html('An error occurred and the upload failed.');
		}
	    }
	},
	onError: function() {
	    progressBar.css("display", "none");
	    msgBox.html('Unable to upload file');
	}
    });
    return uploader;
};

dialogImg = $("#image-setup-dialog").dialog({
    autoOpen: false,
    height: 350,
    width: 300,
    modal: true,
    buttons: {
	"From URL": function() { alert("save file for node: " + $(this).data("id")); },
	"From File": {
	    text: "From File",
	    id: "imageUploadButton"
	},
	"Cancel": function() {
	    dialogImg.dialog("close");
	}
    },
    close: function() {
	form[0].reset();
    }
});

form = dialogImg.find( "form" ).on( "submit", function( event ) {
    event.preventDefault();
    alert("save file for node (by URL): " + $(dialogImg).data("id"));
});

var adjustImageAspectRatio = function(node, image, adjustWidthAlso, serializeAtEnd) {
    image.load(function() {
	var ar = image.width() / image.height();
	if (adjustWidthAlso)
	    node.width(node.height() * ar);
	node.resizable("destroy");
	node.resizable({aspectRatio: ar, stop: function() { serializeImage("#" + node.attr("id")); }});
	if (serializeAtEnd)
	    serializeImage("#" + node.attr("id"));
    });
};

var serializeImage = function(id) {
    var obj = $(id);
    var imageObj = $(id + "Image");
    var dataToSend = JSON.stringify({"top" : obj.offset().top,
				     "left" : obj.offset().left,
				     "width" : obj.width(),
				     "height" : obj.height(),
				     "url" : imageObj.attr("src")
				    });
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
		var state = $.parseJSON(data.state);
		var obj = $(id);
		obj.offset(state);
		obj.width(state.width);
		obj.height(state.height);
		adjustImageAspectRatio(obj, $(id + "Image"), false, false);
		$(id + "Image").attr("src", state.url);
		//alert("loaded from server: " + JSON.stringify(data));
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
    }}).resizable({stop: function() {
	serializeImage(id);
    }}).mouseenter(function(e) {
	showSettingsTimeout = setTimeout(function() {
	    $(id + "Settings").show("blind");
	}, 1000);
    }).mouseleave(function(e) {
	if (showSettingsTimeout !== null) {
	    clearTimeout(showSettingsTimeout);
	    showSettingsTimeout = null;
	}

	$(id + "Settings").hide("blind");
    });

    $(".setup-image").on("click", function() {
	var id = $(this).parent().attr("id");
	var fxSubmit = function() {
	    dialogImg.dialog("close");
	};
	var fxSuccess = function(filename) {
	    adjustImageAspectRatio($("#" + id), $("#" + id + "Image"), true, true);
	    $("#" + id + "Image").attr("src", "resources/" + filename);
	};
	initUploader("#" + id + "Progress", "#" + id + "Message", fxSubmit, fxSuccess);
	dialogImg.data('id', id).dialog("open");
    });
};

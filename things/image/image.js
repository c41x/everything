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
		    msgBox.html(response.desc);
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

var saveImageFromURL = function() {
    var dataToSend = {url: decodeURIComponent($("#image-url").val())}; // get value before dialog close (on close resets form)
    dialogImg.dialog("close");
    $.ajax({
	type: "POST",
	url: "create-resource.php?fromURL=",
	data: dataToSend,
	dataType: "json",
	success: function(data) {
	    var id = dialogImg.data("id");
	    if (data.error) {
		$("#" + id + "Message").html(data.desc);
	    }
	    else {
		adjustImageAspectRatio($("#" + id), $("#" + id + "Image"), true, true);
		$("#" + id + "Image").attr("src", "resources/" + data.id);
	    }
	},
	error: function(a, b, c) {
	    alert("sending error: " + dataToSend);
	    alert(b);
	    alert(c);
	}});
};

dialogImg = $("#image-setup-dialog").dialog({
    autoOpen: false,
    height: 350,
    width: 300,
    modal: true,
    buttons: {
	"From URL": saveImageFromURL,
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

form = dialogImg.find( "form" ).on("submit", function(event) {
    event.preventDefault();
    saveImageFromURL();
});

var adjustImageAspectRatio = function(node, image, adjustWidthAlso, serializeAtEnd) {
    image.load(function() {
	var ar = image.width() / image.height();
	if (adjustWidthAlso)
	    node.width(node.height() * ar);
	node.resizable("destroy");
	node.resizable({aspectRatio: ar, stop: function() { saveImage("#" + node.attr("id")); }});
	if (serializeAtEnd)
	    saveImage("#" + node.attr("id"));
    });
};

var serializeImage = function(id) {
    var obj = $(id);
    var imageObj = $(id + "Image");
    return JSON.stringify({"top" : obj.offset().top,
			   "left" : obj.offset().left,
			   "width" : obj.width(),
			   "height" : obj.height(),
			   "url" : imageObj.attr("src")
			  });
};

var deserializeImage = function(id, data) {
    if (!data.error) {
	var state = $.parseJSON(data.state);
	var obj = $(id);
	obj.offset(state);
	obj.width(state.width);
	obj.height(state.height);
	adjustImageAspectRatio(obj, $(id + "Image"), false, false);
	$(id + "Image").attr("src", state.url);
    }
};

var setupImage = function(id) {
    $(id).draggable({stop: function() {
	saveImage(id);
    }}).resizable({stop: function() {
	saveImage(id);
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

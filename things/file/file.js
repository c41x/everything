var initUploaderFile = function(progressID, messageID, onSubmit, onSuccess) {
    var progressBar = $(progressID),
	msgBox = $(messageID);

    var uploader = new ss.SimpleUpload({
	button: "#fileUploadButton",
	dropzone: "#fileUploadDropZone",
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

var saveFileFromURL = function() {
    var dataToSend = {url: decodeURIComponent($("#file-url").val())}; // get value before dialog close (on close resets form)
    var fileCaption = $("#file-caption").val();
    dialogFile.dialog("close");
    $.ajax({
	type: "POST",
	url: "create-resource.php?fromURL=",
	data: dataToSend,
	dataType: "json",
	success: function(data) {
	    var id = dialogFile.data("id");
	    if (data.error) {
		$("#" + id + "Message").html(data.desc);
	    }
	    else {
		$("#" + id + "Link").attr("href", data.url).html(fileCaption);
		saveFile(id);
	    }
	},
	error: function(a, b, c) {
	    alert("sending error: " + dataToSend);
	    alert(b);
	    alert(c);
	}});
};

dialogFile = $("#file-setup-dialog").dialog({
    autoOpen: false,
    height: 350,
    width: 300,
    modal: true,
    buttons: {
	"Update Caption": function() {
	    $("#" + dialogFile.data("id") + "Link").html($("#file-caption").val());
	    dialogFile.dialog("close");
	},
	"From URL": saveFileFromURL,
	"From File": {
	    text: "From File",
	    id: "fileUploadButton"
	},
	"Cancel": function() {
	    dialogFile.dialog("close");
	}
    },
    close: function() {
	form[0].reset();
    }
});

form = dialogFile.find( "form" ).on("submit", function(event) {
    event.preventDefault();
    saveFileFromURL();
});

var serializeFile = function(id) {
    var obj = $(id);
    var linkObj = $(id + "Link");
    return JSON.stringify({"top" : obj.offset().top,
			   "left" : obj.offset().left,
			   "url" : linkObj.attr("href"),
			   "caption" : linkObj.html()
			  });
};

var deserializeFile = function(id, data) {
    if (!data.error) {
	var state = $.parseJSON(data.state);
	var obj = $(id);
	obj.offset(state);
	$(id + "Link").attr("href", state.url).html(state.caption);
    }
};

var deleteFile = function(id) {
    var dataToSend = extractFileName($(id + "Link").attr("href"));
    $.ajax({
	dataType: "json",
	type: "POST",
	url: "delete-resource.php",
	data: {id: dataToSend},
	success: function(data) {
	    if (data.error) {
		alert("error deleting resource: " + data.desc);
	    }
	},
	error: function(a, b, c) {
	    alert(b);
	    alert(c);
	}
    });
}

var setupFile = function(id) {
    $(id).draggable({stop: function() {
	saveFile(id);
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

    $(".setup-file").on("click", function() {
	var id = $(this).parent().attr("id");
	var fxSubmitFile = function() {
	    dialogFile.data('caption', $("#file-caption").val());
	    dialogFile.dialog("close");
	};
	var fxSuccessFile = function(filename) {
	    $("#" + id + "Link").attr("href", "resources/" + filename).html(dialogFile.data('caption'));
	    saveFile("#" + id);
	};
	initUploaderFile("#" + id + "Progress", "#" + id + "Message", fxSubmitFile, fxSuccessFile);
	dialogFile.data('id', id).dialog("open");
    });
};

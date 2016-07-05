function escapeTags( str ) {
    return String( str )
        .replace( /&/g, '&amp;' )
        .replace( /"/g, '&quot;' )
        .replace( /'/g, '&#39;' )
        .replace( /</g, '&lt;' )
        .replace( />/g, '&gt;' );
}

var initUploader = function(btnID, progressID, messageID) {
    var btn = $(btnID),
        progressBar = $(progressID),
        msgBox = $(messageID);

    var uploader = new ss.SimpleUpload({
        button: btn,
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
            msgBox.innerHTML = '';
            btn.innerHTML = 'Uploading...';
        },
        onComplete: function(filename, response) {
            btn.innerHTML = 'Choose Another File';
            progressBar.css("display", "none");

            if (!response) {
                msgBox.innerHTML = 'Unable to upload file';
                return;
            }

            if (response.error === false) {
                msgBox.innerHTML = '<strong>' + escapeTags( filename ) + '</strong>' + ' successfully uploaded. ID = ' + response.id;

            }
            else {
                if (response.desc) {
		    msgBox.innerHTML = escapeTags( response.desc );
                }
		else {
		    msgBox.innerHTML = 'An error occurred and the upload failed.';
                }
            }
        },
        onError: function() {
            progressBar.css("display", "none");
            msgBox.innerHTML = 'Unable to upload file';
        }
    });
};

dialogImg = $("#image-setup-dialog").dialog({
    autoOpen: false,
    height: 350,
    width: 300,
    modal: true,
    buttons: {
        "From URL": function() { alert("save file for node: " + $(this).data("id")); },
        "From File": function() { alert("from disc for node" + $(this).data("id")); },
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

var serializeImage = function(id) {
    var obj = $(id);
    var imageObj = $(id + "Image");
    var dataToSend = JSON.stringify({"top" : obj.offset().top,
				     "left" : obj.offset().left,
                                     "width" : obj.width(),
                                     "height" : obj.height(),
                                     //"url" : imageObj.attr("src")
                                     "url" : "resources/5772c003824dc.jpg"
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
                var objImage = $(id + "Image");
		obj.offset(state);
                obj.width(state.width);
                obj.height(state.height);
                objImage.load(function() {
                    var ar = objImage.width() / objImage.height();
                    obj.resizable("destroy");
                    obj.resizable({aspectRatio: ar});
                });
                $(id + "Image").attr("src", state.url);
		alert("loaded from server: " + JSON.stringify(data));
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
	dialogImg.data('id', $(this).parent().attr("id"))
            .dialog("open");
    });
};

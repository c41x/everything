var serializeLink = function(id) {
    var obj = $(id);
    var dataToSend = JSON.stringify({
        "top" : obj.offset().top,
	"left" : obj.offset().left,
        "id" : toID($(id + "Link").attr("href")),
        "desc": $(id + "Link").html()
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

var deserializeLink = function(id) {
    $.ajax({
	dataType: "json",
	url: "get-node.php?id=" + toID(id),
	success: function(data) {
            if (data.error) alert(data.desc);
            else {
		var json = $.parseJSON(data.state);alert(JSON.stringify(data));
	        $(id).offset(json);
		$(id + "Link").attr("href", "index.php?id=" + json.id);
		$(id + "Link").html(json.desc);
		onNodeDeserialized();
            }
	},
	error: function(a, b, c) {
	    alert(b);
	    alert(c);
	}
    });
};

var setupLink = function(id) {
    $(id).draggable({
        stop: function() {
	    serializeLink(id);
        }
    });
};

var addUser = function(id, desc) {
    $.ajax({
	url: "create-page.php",
	method: "POST",
	dataType: "json",
	data: { title: $("#new-page-id").val() },
	success: function(data) {
	    if (data.error) alert(data.desc);
	    else {
		alert("created page with ID = " + data.id);
		alert("desc = " + desc);
		alert("id = " + id);
		$("#" + id + "Link").attr('href', "index.php?id=" + data.id);
		$("#" + id + "Link").html(desc);
	    }
	},
	error: function(a, b, c) {
	    alert(b);
	    alert(c);
	}
    });
    dialog.dialog( "close" );
}

dialog = $( "#dialog-form" ).dialog({
    autoOpen: false,
    height: 250,
    width: 300,
    modal: true,
    buttons: {
        "Set Link": function () { addUser($(this).data("id"), $("#new-page-title").val()); },
        Cancel: function() {
            dialog.dialog( "close" );
        }
    },
    close: function() {
        form[ 0 ].reset();
    }
});

form = dialog.find( "form" ).on( "submit", function( event ) {
    event.preventDefault();
    alert("addin usa");
});

$( ".create-page" ).button().on( "click", function() {
    dialog.data('id', $(this).parent().attr("id"))
        .dialog( "open" );
});

$( ".link-page" ).button().on( "click", function() {
    dialog.data('id', $(this).parent().attr("id"))
        .dialog( "open" );
});

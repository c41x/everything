var serializeLink = function(id) {
    var obj = $(id);
    return JSON.stringify({
	"top" : obj.offset().top,
	"left" : obj.offset().left,
	"id" : toID($(id + "Link").attr("href")),
	"desc": $(id + "Link").html()
    });
};

var deserializeLink = function(id, data) {
    if (!data.error) {
	var json = $.parseJSON(data.state);
	$(id).offset(json);
	var link = $(id + "Link");
	link.attr("href", "index.php?id=" + json.id);
	link.html(json.desc);
    }
};

var deleteLink = function(id) {
}

var showSettingsTimeout = null;

var setupLink = function(id) {
    $(id).draggable({
	stop: function() {
	    saveLink(id);
	}
    }).mouseenter(function(e) {
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

    $(".setup-page").on( "click", function() {
	dialog.data('id', $(this).parent().attr("id"))
	    .dialog( "open" );
    });
};

var processLink = function(id, desc) {
    $.ajax({
	url: "create-page.php",
	method: "POST",
	dataType: "json",
	data: { title: $("#new-page-id").val() },
	success: function(data) {
	    if (data.error) alert(data.desc);
	    else {
		var link = $("#" + id + "Link");
		link.attr('href', "index.php?id=" + data.id);
		link.html(desc);
		saveLink("#" + id);
	    }
	},
	error: function(a, b, c) {
	    alert(b);
	    alert(c);
	}
    });
    dialog.dialog( "close" );
}

dialog = $("#link-setup-dialog").dialog({
    autoOpen: false,
    height: 250,
    width: 300,
    modal: true,
    buttons: {
	"Set Link": function () { processLink($(this).data("id"), $("#new-page-title").val()); },
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
    processLink($(dialog).data("id"), $("#new-page-title").val());
});


$("#new-page-id").autocomplete({source: "get-pages.php"});

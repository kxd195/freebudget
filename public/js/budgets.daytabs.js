$(function() {
	calcTotals();
	
	$("#tagVersionModal").on("shown.bs.modal", function () {
		$("#tagVersionModal :input:first").focus();
	})
	
	$("#shareCreateButton").on("click", createShare);
	initShareClipboard();
	
	// when the scene modal is shown, load input fields with data
	$("#sceneModal").on("show.bs.modal", function (event) {
		var button = $(event.relatedTarget);
		var scene_id = button.data("scene");
		var modal = $(this);

		// set the selects according to the modify button clicked
		modal.find("#scene-name").val(button.data("scene-name")).focus();
		modal.find("#location").val(button.data("location"));
		modal.find("#description").val(button.data("description"));
		modal.find("#notes").val(button.data("notes"));
		modal.find("#day-name").html(button.data("day-name"));
		modal.find("[name='day_id']").val(button.data("day-id"));
		modal.find("form").attr("action", modal.find("form").attr("action").replace(/scene-info-here/gi, scene_id));
	});
});

function initShareClipboard() {
	var clipboard = new Clipboard("#hyperlinkCopyButton", {
	    container: document.getElementById("shareCreatedModal")
	});
	
	clipboard.on('success', function(e) {
		$("#hyperlinkCopyButton").removeClass("btn-primary").addClass("btn-success")
			.find(".copy-text").html("Copied!");
	});
}

function dayQuickjump() {
	var day_id = $("#day-quickjump").val();
	
	if (day_id === "ALL")
		$("#dayTable tbody[data-day-id]").show();
	else {
		$("#dayTable tbody[data-day-id='" + day_id +"']").show();
		$("#dayTable tbody[data-day-id!='" + day_id +"']").hide();
	}

	calcTotals();
}

function calcTotals() {
	var grandTotal = 0;
	$("#dayTable tbody[data-day-id]:visible td[headers='amount_col'].line-item-amount").each(function() {
		grandTotal += parseAmount($(this).html());
	});

	$("#grandTotal").html(grandTotal.formatMoney(2));
}


function createShare() {
	var CSRF_TOKEN = $("meta[name='csrf-token']").attr("content");

	$.ajax({
	    url: $("#shareModal form").attr("action"),
	    type: 'POST',
	    data: {
	    		_token: CSRF_TOKEN,
	    		budget_id: $("input[name='budget_id']").val(),
	    		modifiable: $("input[name='modifiable']").val(),
	    		expires_after: $("input[name='expires_after']").val(),
	    		budget_version_id: $("input[name='budget_version_id']").val(),
	    	},
	    dataType: 'JSON',
	    success: function (data) {
	    		$("#shareHyperlink").val(data.hyperlink);
	    		$("#shareAccess").val(data.modifiable ? "Read & Write" : "Read-only");
	    		$("#shareExpiresAt").val(data.expires_at != null ? data.expires_at : "Indefinite");
	    		$("#shareModal").modal('hide');
	    		$("#shareCreatedModal").modal('show');
	    }
	});
}
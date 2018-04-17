$(function() {
	$(".nav-tabs").scrollingTabs({
		tabClickHandler: function (e) {
			var day_id = $(this).attr("aria-controls");
			$("#day-quickjump option[value='#" + day_id + "']").prop("selected", true);
		  }  
	});
	
	$("#budget_row .panel, .row-day-info .panel").matchHeight();

	$("#tagVersionModal").on("shown.bs.modal", function () {
		$("#tagVersionModal :input:first").focus();
	})
	
	$("#shareCreateButton").on("click", createShare);
	initShareClipboard();
	
	if ($(".nav-tabs li[role='presentation'].active").length === 0)
		setDefaultDayTab();
	
	// when the scene modal is shown, load input fields with data
	$('#sceneModal').on("show.bs.modal", function (event) {
		var button = $(event.relatedTarget);
		var scene = button.data("scene");
		var day_id = button.data("day-id");
		var modal = $(this);
		  
		modal.find("select[name='scene'] option[value='" + scene + "']").attr("selected", true);
		modal.find("select[name='day_id'] option[value='" + day_id + "']").attr("selected", true);
		modal.find("input[name='old_day_id']").val(day_id);
		modal.find("input[name='old_scene']").val(scene);
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

function setDefaultDayTab() {
	var tabSet = false;
	$("[role='tab']").each(function() {
		if (tabSet)
			return;
		
		$currDate = new moment($(this).data("actualdate"));
		if ($currDate.isSameOrAfter(new moment())) {
			$(this).click();
			tabSet = true;
		}
	});
}

function dayQuickjump() {
	var day_id = $("#day-quickjump").val();
	
	$("a[href='" + day_id + "'][role='tab']").click();
	$(".nav-tabs").scrollingTabs('refresh');

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
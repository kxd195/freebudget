$(function() {
	$("#actualdate").datepicker();
});

function checkDateConflict() {
	var date = $("[name='actualdate']").val();
	var original_date = $("[name='originaldate']").val();

	// if date hasn't changed, dont bother checking
	if (date === original_date) {
		$("#pane-dateConlictWarning").addClass("hidden");
		return;
	}

	var conflict = false;
	for (var i=0; i < existingDays.length; i++) {
		if (existingDays[i] === date)
			conflict =true;
	}
	
	if (conflict)
		$("#pane-dateConlictWarning").removeClass("hidden");
	else
		$("#pane-dateConlictWarning").addClass("hidden");
	
}
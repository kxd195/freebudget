$(function() {
	toggleView();
});

function toggleView() {
	var isDailyRate = $("[name='is_daily'][value='1']").is(":checked");

	slide("#pane-min-hours", !isDailyRate);
}
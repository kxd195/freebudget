$(function() {
	toggleType();
	toggleAssistantOptions();
});

function toggleType() {
	var type = $("[name='type'] option:selected").val();
	slide("#pane-qty", type.indexOf("Series") >= 0 || type === "Feature");
	slide("#pane-season", type.indexOf("Series") >= 0);
	$("#pane-qty label").html(type.indexOf("Series") >= 0 ? "# of Episodes:" : "# of Shoot Days:");
}

function toggleAssistantOptions() {
	slide("#pane-assistant-schedule", $("#assistant_rate_unit").val() === "hour");
	$("#pane-suboptions div.panel").matchHeight();
}
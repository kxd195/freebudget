$(function() {
	toggleType();
});

function toggleType() {
	var type = $("[name='type'] option:selected").val();
	slide("#pane-qty", type === "TV Series" || type === "Feature");
	$("#pane-qty label").html(type === "TV Series" ? "Episodes:" : "Shoot Days:");
}
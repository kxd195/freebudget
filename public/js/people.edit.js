$(function() {
	toggleScene();
});

function toggleScene(obj) {
	if ($(obj).is("[type='radio']"))
		$(obj).siblings("input[type='text'], select").focus();
	else
		$(obj).siblings("input[type='radio']").attr("checked", true);

	if ($(obj).is("select") || obj == null) {
		var scene = $("#scene_id").find("option:selected");
		$("#scene-existing-name").html( scene.text() );
		$("#scene-existing-location").html( scene.data("location") );
		$("#scene-existing-description").html( scene.data("description") );
		$("#scene-existing-notes").html( scene.data("notes") );
	}

	slide("#pane-scene-existing", $("[name='scene_option'][value='select']").is(":checked"));
	slide("#pane-scene-new", $("[name='scene_option'][value='new']").is(":checked"));
}
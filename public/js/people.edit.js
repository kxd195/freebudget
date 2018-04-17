function toggleScene(obj) {
	if ($(obj).is("[type='radio']"))
		$(obj).siblings("input[type='text'], select").focus();
	else
		$(obj).siblings("input[type='radio']").attr("checked", true);
}
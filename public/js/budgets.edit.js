function toggleUntilDate(obj) {
	if ($(obj).is("[type='number']") || $(obj).is("[type='text']")) {
		$(obj).closest(".form-group").find("input[type='radio']").click();
	}
	
	if ($(obj).is("[value='enddate']")) {
		$("[name='enddate']").focus();
		$("[name='num_days']").val("");
	} else {
		$("[name='num_days']").focus();
		$("[name='enddate']").val("");
	}
}

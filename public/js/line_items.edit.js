$(function() {
	refreshMinHoursTooltip();
	
	$("[id^='qty'], [id^='hours'], [id^='cost']").on("keyup mouseup change", function() {
		recalculate(this);
	});
	
	$(".master-row :input").attr("disabled", true);
	
	$("#submitButton").on("click", preSubmit);
	
	if ($("tbody tr").length === 0)
		$("#addRowButton").click();

	$.fn.select2.defaults.set("theme", "bootstrap");
	$("[id^='rate_class_id']:visible").select2();
});

function preSubmit() {
	$("[name$='[cost]']:visible").each(function() {
		$(this).val(parseAmount($(this).val()));
	});
}

var dayDropdownHolder;

function copyToMultipleDays() {
	var dayIds = [];
	$("[name='multiple_day_id']:checked").each(function() {
		dayIds.push($(this).val());
	})
	
	if (dayDropdownHolder == null)
		dayDropdownHolder = $("[name='day_id'] option");
	
	if (dayIds.length === 0 || dayIds.length === 1) {
		$("[name='day_id']")
			.empty()
			.append(dayDropdownHolder);
		
		if (dayIds.length === 1)
			$("[name='day_id'] option[value='" + dayIds[0] + "']").prop("selected", true);

	} else
		$("[name='day_id']")
			.empty()
			.append("<option value='" + dayIds + "' selected='selected'>Multiple Days</option>");
}

function selectAllDays() {
	$("[name='multiple_day_id']").prop("checked", $("[name='multiple_day_select_all']").is(":checked"));
}

function removeRow(obj) {
	var row = $(obj).closest("tr");
	var iteration = parseInt( $(row).find("[id^='qty_']").attr("id").replace(/[\D]/gi, "") );
	
	for (var next_row = $(row).next(); next_row.length !== 0; next_row = $(next_row).next()) {
		$(next_row).find(":input").each(function() {
			if ($(this).attr("name") != null)
				$(this).attr("name", $(this).attr("name").replace(/[\d]/gi, iteration));
			
			if ($(this).attr("id") != null)
				$(this).attr("id", $(this).attr("id").replace(/[\d]/gi, iteration));
		});
		
		iteration++;
		
	}
	
	$(row).remove();
}

function addRow(obj) {
	var new_row = $(".master-row").clone(true);
	var tbody = $(obj).closest("table").find("tbody");
	var new_row_num = $(tbody).find("tr").length;
	
	$(new_row).removeClass("hidden master-row");
	$(new_row).find(":input").each(function() {
		$(this)
			.attr("name", $(this).attr("name").replace(/\[\]/gi, "[" + new_row_num + "]"))
			.attr("id", $(this).attr("id") + "_" + new_row_num)
			.attr("disabled", false);
	});
	
	$(tbody).append(new_row);
	$(new_row).data("iteration", new_row_num);

	// init the new tooltips
	$("body").tooltip({
		selector: "[data-toggle='tooltip']",
	});

	$(new_row).find("[id^='rate_class_id']:visible").select2();

	refreshMinHoursTooltip(new_row_num);
	return new_row_num;
}

function refreshMinHoursTooltip(iteration) {
	var selector = iteration != null ? "#hours_" + iteration : "[id^='hours'][data-toggle='tooltip']:visible";
	$(selector).each(function() {
		var min_hours = parseFloat($(this).attr("min"));
		$(this)
			.attr("title", $(this).data('default-title') + ' ' + min_hours.toFixed(1))
			.tooltip('fixTitle');
		
	});
}

function toggleCost(obj) {
	var iteration = $(obj).attr("id").replace(/[\D]/gi, "");
	var cost_override = $("#cost_overridden_" + iteration).is(":checked");
	var cost_secondrate = $("#cost_secondrate_" + iteration).is(":checked");
	$("#cost_" + iteration).attr("readonly", !cost_override);
	
	if (cost_secondrate && $(obj).is("[id^='cost_secondrate']")) {
		$("#cost_original_" + iteration).val($("#cost_" + iteration).val());
		var amt = parseAmount($("#cost_" + iteration).val());
		$("#cost_" + iteration).val((amt / 2).toFixed(2));
	} else if (!cost_secondrate && $(obj).is("[id^='cost_secondrate']")) {
		$("#cost_" + iteration).val($("#cost_original_" + iteration).val());
		$("#cost_original_" + iteration).val("");
	}
	
	if (cost_override) 
		$("#cost_" + iteration).focus();
	else
		recalculate(obj);
}

function loadFromRateClass(obj) {
	var iteration = $(obj).attr("id").replace(/[\D]/gi, "");
	var rate_class_default = parseFloat($("#rate_class_id_" + iteration + " option:selected").data("rate"));
	var rate_class_min_hours = parseFloat($("#rate_class_id_" + iteration + " option:selected").data("hours"));
	var cost_override = $("#cost_overridden_" + iteration).is(":checked");
	var curr_hours = parseFloat($("#hours_" + iteration).val());
	var curr_cost = parseFloat($("#cost_" + iteration).val());
	
	$("#hours_" + iteration).prop("min" , rate_class_min_hours);
	refreshMinHoursTooltip(iteration);
	
	// only set the hours if min_hours is specified
	if (rate_class_min_hours > 0 && curr_hours < rate_class_min_hours)
		$("#hours_" + iteration).val(rate_class_min_hours.toFixed(1));
	
	// only set the rate if it's not overridden or it's the rate-class triggering this
	if (!cost_override || $(obj).is("#rate_class_id_" + iteration)) {
		var no_default_set = rate_class_default === 0;

		if (!no_default_set || curr_cost === 0)
			$("#cost_" + iteration).val(rate_class_default.toFixed(2));
	
		// if no default rate specified, automatically allow rate entry
		$("#cost_overridden_" + iteration).prop("checked", no_default_set);
		$("#cost_secondrate_" + iteration).prop("checked", false);

		$("#cost_" + iteration).prop("readonly", !no_default_set);
	}
}

function recalculate(obj) {
	var iteration = $(obj).attr("id").replace(/[\D]/gi, "");
	
	// only load the rate class if it was the one who triggered this recalculate
	if ($(obj).is("#rate_class_id_" + iteration) || $(obj).is("#cost_overridden_" + iteration))
		loadFromRateClass(obj);

	var isAddon = $("#rate_class_id_" + iteration + " option:selected").data("addon") === 1;
	showHide($(obj).closest("tr").find(".quick-action-buttons"), !isAddon);

	var hours = isNaN($("#hours_" + iteration).val()) || $("#hours_" + iteration).val().length === 0 ? 0 : parseInt($("#hours_" + iteration).val());
	var url = CONTEXT_PATH + "/api/calcPayroll/" + hours;
	
	$.getJSON(url, function(jsonObj) {
		$("#payroll_" + iteration).val(jsonObj.result);
		
		var qty = parseInt($("#qty_" + iteration).val());
		var payroll = parseFloat($("#payroll_" + iteration).val());
		var cost = parseAmount($("#cost_" + iteration).val());

		$("#amount_" + iteration).val((qty * payroll * cost).toFixed(2));
	});

}

function calcSecondCategory() {
	var highest_cost = 0;
	var highest_cost_row = 0;
	
	$("[id^='cost_'][type='number']").each(function() {
		var iteration = $(this).attr("id").replace(/[\D]/gi, "");
		var curr_cost = parseFloat($(this).val());
		
		if (parseInt($("#rate_class_id_" + iteration + " option:selected").data("addon")) === 0) {
			if (highest_cost < curr_cost) {
				highest_cost = curr_cost;
				highest_cost_row = iteration;
			}
		}
	});
	
	$("[id^='cost_'][type='number']").each(function() {
		var iteration = $(this).attr("id").replace(/[\D]/gi, "");
		var curr_cost = parseFloat($(this).val());
		
		if (parseInt($("#rate_class_id_" + iteration + " option:selected").data("addon")) === 0
				&& iteration !== highest_cost_row) {
			if (highest_cost > curr_cost) {
				$(this)
					.data("original-cost", curr_cost)
					.val((curr_cost / 2).toFixed(2));
			}
		}
	});
}

function doQuickAction(obj, code) {
	var iteration = $(obj).closest("tr").data("iteration");
	var isNonUnion = $("#rate_class_id_" + iteration + " option:selected").data("code").indexOf("NU") === 0;
	var new_row_num = addRow(obj);
	
	if (isNonUnion && code === "WF")
		$("#hours_" + new_row_num).val("4.0");
	
	$("#qty_" + new_row_num).val($("#qty_" + iteration).val());
	$("#cost_" + new_row_num).val($("#cost_" + iteration).val());

	var selectedVal = $("#rate_class_id_" + new_row_num).find("option[data-code='" + code + "']").val();
	$("#rate_class_id_" + new_row_num).val(selectedVal).trigger("change");
}

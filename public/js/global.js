$(function() {
	$('[data-toggle="tooltip"]:visible').tooltip();
	$.fn.datepicker.defaults.format = "yyyy-mm-dd";
	$.fn.datepicker.defaults.orientation = "bottom left";
	$.fn.datepicker.defaults.autoclose = true;
	
	$.jMaskGlobals.watchDataMask = true;
	
	$(".date").find("input[type='text'], input[type='date']")
		.attr("placeholder", $.fn.datepicker.defaults.format.toUpperCase())
		.mask("0000-00-00");
	
	$("a[data-toggle='tab']").on("shown.bs.tab", function (e) {
		// init the new tooltips
		$("body").tooltip({
			selector: "[data-toggle='tooltip']",
		});
	});
});

function slide(selector, show) {
	if (show)
		$(selector).slideDown("slow", function() {
			$(this).find(":input").attr("disabled", false);
		});
	else
		$(selector).slideUp("slow", function() {
			$(this).find(":input").attr("disabled", true);
		});

}

function showHide(selector, show) {
	if (show)
		$(selector).show("fast", function() {
			$(this).find(":input").attr("disabled", false);
		});
	else
		$(selector).hide("fast", function() {
			$(this).find(":input").attr("disabled", true);
		});

}
function parseAmount(val) {
	return parseFloat(val.replace(/,/gi, ""));
}
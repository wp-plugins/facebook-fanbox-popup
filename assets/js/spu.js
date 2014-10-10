var spu_count = 0;
var spu_counter ='';
	function facebook_fanbox_popup(options) {
		var defaults = { days_no_click : "7" };
		var options = jQuery.extend(defaults, options);
		window.options = options;
		
		var cook = readCookie('fbfanboxp');
		//var waitCook = readCookie('spuwait');

		if (cook != 'true') {
			var windowWidth = document.documentElement.clientWidth;
			var windowHeight = document.documentElement.clientHeight;
			var popupHeight = jQuery("#ffbp").height();
			var popupWidth = jQuery("#ffbp").width();
			jQuery("#ffbp").css({
				"position": "fixed",
				"top": (windowHeight / 2 - popupHeight / 2) - 30,
				"left": windowWidth / 2 - popupWidth / 2
			});
			jQuery("#ffbp-bg").css({
				"height": windowHeight + 30
			});
			jQuery("#ffbp-bg").css({
				"opacity": defaults.opacity
			});
			jQuery("#ffbp-bg").fadeIn("slow");
			jQuery("#ffbp").fadeIn("slow");
		}
 
		if( parseInt(defaults.s_to_close) > 0 )
		{
			spu_count=defaults.s_to_close;
			spu_counter = setInterval(function(){spu_timer(defaults)}, 1000);
		}
		return true;
	}


function fbfanboxp( days ) {
	days = typeof days !== 'undefined' ? days : 7;
	createCookie('fbfanboxp', 'true', days);
	
	jQuery("#ffbp-bg").fadeOut("slow");
	jQuery("#ffbp").fadeOut("slow");
}

function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else var expires = "";
	document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

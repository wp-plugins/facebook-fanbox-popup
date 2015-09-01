var spu_count = 0;
var spu_counter ='';


function facebook_fanbox_popup(options) {
	var defaults = { days_no_click : "7" };
	var options = jQuery.extend(defaults, options);
	window.options = options;
	
	var cook = readCookie('fbfanboxp');
	//var waitCook = readCookie('spuwait');

	if (cook != 'true' || options.days_no_click == 0 ) {
		var windowWidth = document.documentElement.clientWidth;
		var windowHeight = document.documentElement.clientHeight;

		//var popupHeight = jQuery("#ffbp").height();
		//var popupHeight = 502;
		////var popupHeight = 216;
		//alert(popupHeight + " - " + windowHeight);
		//var popupWidth = jQuery("#ffbp").width();
		var popupHeight =  window.options.stream == 1 ? 502 : 216;
		var popupWidth = options.width;

		if( windowWidth <= 600 ){
			popupHeight =  window.options.stream == 1 ? 390 : 216;
			popupWidth = 300;
			jQuery("#ffbp").attr("data-width","300");
			jQuery("#ffbp").attr("data-height","390");
		}

		jQuery("#ffbp").css({
			"position": "fixed",
			"top": window.options.stream == 1 ? (windowHeight / 2 - popupHeight / 2) + 20  : (windowHeight / 2 - popupHeight / 2) - 50,
			"left": windowWidth / 2 - popupWidth / 2,
			"width":options.width
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


	if( window.options.disabled_scroll == 1 ){
		lockScroll();
	}

	return true;
}


function fbfanboxp( days ) {
	days = typeof days !== 'undefined' ? days : 7;
	createCookie('fbfanboxp', 'true', days);
	
	jQuery("#ffbp-bg").fadeOut("slow");
	jQuery("#ffbp").fadeOut("slow");

	if( window.options.disabled_scroll == 1 ){
		unlockScroll();
	}
	
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


function lockScroll(){
	var $ = jQuery;
  $html = $('html'); 
  $body = $('body'); 
  var initWidth = $body.outerWidth();
  var initHeight = $body.outerHeight();

  var scrollPosition = [
      self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
      self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop
  ];
  $html.data('scroll-position', scrollPosition);
  $html.data('previous-overflow', $html.css('overflow'));
  $html.css('overflow', 'hidden');
  window.scrollTo(scrollPosition[0], scrollPosition[1]);   

  var marginR = $body.outerWidth()-initWidth;
  var marginB = $body.outerHeight()-initHeight; 
  $body.css({'margin-right': marginR,'margin-bottom': marginB});
} 

function unlockScroll(){
	var $ = jQuery;
	$html = $('html');
	$body = $('body');
	$html.css('overflow', $html.data('previous-overflow'));
	var scrollPosition = $html.data('scroll-position');
	window.scrollTo(scrollPosition[0], scrollPosition[1]);    

	$body.css({'margin-right': 0, 'margin-bottom': 0});
}


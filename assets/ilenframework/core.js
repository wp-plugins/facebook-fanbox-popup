jQuery(document).ready( function() {
 
	jQuery('.ilentheme-options #tabs,.ilenplugin-options #tabs')
        .tabs({  show: function(event, ui) {

            var lastOpenedPanel = $(this).data("lastOpenedPanel");

            if (!$(this).data("topPositionTab")) {
                $(this).data("topPositionTab", $(ui.panel).position().top)
            }         

            //Dont use the builtin fx effects. This will fade in/out both tabs, we dont want that
            //Fadein the new tab yourself            
            $(ui.panel).hide().fadeIn(50);

            if (lastOpenedPanel) {

                // 1. Show the previous opened tab by removing the jQuery UI class
                // 2. Make the tab temporary position:absolute so the two tabs will overlap
                // 3. Set topposition so they will overlap if you go from tab 1 to tab 0
                // 4. Remove position:absolute after animation
                lastOpenedPanel
                    .toggleClass("ui-tabs-hide")
                    .css("position", "absolute")
                    .css("top", $(this).data("topPositionTab") + "px")
                    .fadeOut(50, function() {
                        $(this)
                        .css("position", "");
                    });

            }

            //Saving the last tab has been opened
            $(this).data("lastOpenedPanel", $(ui.panel));

        } } )
        .addClass('ui-tabs-vertical ui-helper-clearfix');




    //Custom
    // =save theme options
    jQuery(".ilentheme-options .btn_save,.ilenplugin-options .btn_save").on("click",function(event){
        event.preventDefault();
        jQuery( this ).find("i").addClass("spinInfinite");
        ilenvalidatorsubmit();
        document.frmsave.submit();
    });
 

    // for plugin
    jQuery(".ilenplugin-options .btn_reset, .ilentheme-options .btn_reset").on("click",function(event){
        event.preventDefault();
        if( confirm( jQuery(this).attr("data-me") ) ){
            jQuery( this ).find("i").addClass("spinInfinite");
            document.frmreset.submit();
        }
    });
    // end -> ONLY THEME 'plugin-fresh.css'



    var formfield;
    // upload file >3.5
    jQuery('.ilentheme-options .upload_image_button,.ilentheme-options .upload_image_button_complete, .ilenplugin-options .upload_image_button,.ilenplugin-options .upload_image_button_complete').on("click",function( event ){  

		 	event.preventDefault();
 			formfield = jQuery(this).prev().attr('id');
 			var button_this = jQuery(this);

		    var custom_uploader = wp.media({
		        title: jQuery(button_this).attr('data-title'),
		        button: {
		            text: jQuery(button_this).attr('data-button-set')
		        },
		        multiple: false  // Set this to true to allow multiple files to be selected
		    })
		    .on('select', function() {
		        var attachment = custom_uploader.state().get('selection').first().toJSON();

		        jQuery("#"+formfield).val(attachment.url);
                if( jQuery(button_this).hasClass("upload_image_button_complete") ){
                    jQuery(button_this).next(".preview").html("<span class='admin_delete_image_upload admin_delete_image_upload_complete'>✕</span>");
                    jQuery(button_this).next(".preview").css("background-image","url("+attachment.url+")");
                    jQuery(button_this).next(".preview").css("height","200px");
                }else{
                    jQuery(button_this).parent().find(".preview").html("<img src='"+attachment.url+"' /><span class='admin_delete_image_upload'>✕</span>");
                }
		    })
		    .open();

	});

    jQuery('.ilenplugin-options .upload_image_default,.ilentheme-options .upload_image_default').on('click',function(){
        jQuery(this).prev().prev().val( jQuery(this).attr("image-default") );
        jQuery(this).parent().find(".preview").html("<img src='"+jQuery(this).attr("image-default")+"' /><span class='admin_delete_image_upload'>✕</span>");
    });

    // end upload >3.5


	// upload file old
	var button_this;
    var orig_send_to_editor = window.send_to_editor;
	jQuery('.ilentheme-options .upload_image_button_old,.ilenplugin-options .upload_image_button_old').on("click",function( event ){ 
		 button_this = jQuery(this); 
		 formfield = jQuery(this).prev().attr('id');
		 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');

        window.send_to_editor = function(html) {
             imgurl = jQuery('img',html).attr('src');
             jQuery("#"+formfield).val(imgurl);
             jQuery(button_this).parent().find(".preview").html("<img src='"+imgurl+"' /><span class='admin_delete_image_upload'></span>");
             tb_remove();

             window.send_to_editor = orig_send_to_editor;
        }

		 return false;
	});
	
	// end upload file old


	// delete upload clear input
	jQuery(".ilentheme-options .admin_delete_image_upload,.ilenplugin-options .admin_delete_image_upload").live("click",function(){
	    jQuery(this).parent().parent().find('.theme_src_upload').val('');
	    jQuery(this).prev().fadeOut(300);
	    jQuery(this).fadeOut(300);
	});
    jQuery(".ilentheme-options .admin_delete_image_upload_complete, .ilenplugin-options .admin_delete_image_upload_complete").live("click",function(){
        jQuery(this).parent().parent().find('.theme_src_upload').val('');
        jQuery(this).parent().css("background-image","url()");
        jQuery(this).parent().css("height","20px");
        jQuery(this).fadeOut(300);
    });

    // select2 change event in background_complete
    jQuery(".ilentheme-options .background_complete .select2_background_complete, .ilenplugin-options .background_complete .select2_background_complete").on("change",function(){
        jQuery(this).parent().parent().next().find(".preview").css(jQuery(this).attr('data-attribute'),jQuery(this).val());
    });
    



	// select radio image (active)
	jQuery(".ilentheme-options .radio_image_selection,.ilenplugin-options .radio_image_selection").on("click",function( event){

		event.preventDefault();
		var class_ref;
		var img_obj;


		class_ref = jQuery(this).attr("data-id");
		img_obj = jQuery(this);

		jQuery("."+class_ref).each(function(){
			jQuery(this).removeClass("active");
		});

		jQuery(img_obj).addClass("active");
		jQuery(img_obj).next().attr("checked","checked");

	});

    // select radio bg pattern (active)
    jQuery(".item_pattern_bg").on("click",function( event){

        event.preventDefault();
        var class_ref;
        var img_obj;
        var obj_wrap;
        var obj_wrap_class;

        obj_wrap_class = jQuery(this).parent().parent().attr("class");
        obj_wrap = jQuery(this).parent().parent();
        //class_ref = jQuery(this).attr("data-id");
        img_obj = jQuery(this);
        //alert("."+obj_wrap_class+" .item_pattern_bg");
        jQuery( "."+obj_wrap_class+" .item_pattern_bg" ).each(function(){
            jQuery(this).removeClass("active");
            jQuery(this).next().attr('checked', false);

        });

        jQuery(img_obj).addClass("active");
        jQuery(img_obj).next().attr("checked","checked");

    });

 

	// set input an colorpicker
	jQuery('.ilentheme-options .theme_color_picker, .ilenplugin-options .theme_color_picker').wpColorPicker();


	// if exists div class 'mesaggebox' delete element whth effect
	if ( jQuery('.ilentheme-options div.messagebox').length ) {

		setTimeout(function() {
		    jQuery('.ilentheme-options div.messagebox').slideUp(1000, function(){
			    jQuery(this).remove();
			});
		 }, 2000);

	}




    // Background custom & pattern
    jQuery(".switch-label").on("click",function(){

        var opt =  jQuery(this).prev().attr("value");
        var obj1 =  jQuery(this).parent().parent().find(".custom_bg_wrap");
        var obj2 =  jQuery(this).parent().parent().find(".pattern_bg_wrap");
        jQuery(obj1).css("display","none");
        jQuery(obj2).css("display","none");
        //jQuery().fadeOut( 1500, function() {

            //jQuery(obj).css("display","none");

            var obj_move;
            if( opt == "2"){
                //obj_move = jQuery(this).parent().parent().find(".custom_bg_wrap");
                obj1.css("display","block");    
            }else if( opt = "1" ){
                //obj_move = jQuery(this).parent().parent().find(".pattern_bg_wrap");
                obj2.css("display","block");
               
            }
        //});
        
        

    });



    // Fix color picker in background_complete
    jQuery(".ilentheme-options .background_complete .wp-color-result, .ilenplugin-options .background_complete .wp-color-result").on("click",function(){

        if( jQuery(this).hasClass('wp-picker-open') )
            jQuery(this).next().next('.wp-picker-holder').css({
              'display'         : 'block',
              'overflow'        : 'hidden',
              'margin-bottom'   : '20px'
            });
        else
            jQuery(this).next().next('.wp-picker-holder').css({
              'display'         : 'inherit',
              'overflow'        : 'inherit',
              'margin-bottom'   : '0'
            });

    });
    



    // Fix color picker double
    /*jQuery(".ilentheme-options .color_hover .wp-color-result, .ilenplugin-options .color_hover .wp-color-result").on("click",function(){
        var objCol = jQuery(this).parent().parent();

        if( jQuery(this).hasClass('wp-picker-open') ){
            alert(1);
            if( jQuery(this).parent().parent().hasClass("color_hover_color") ){
                alert(2);
                jQuery(objCol).parent().parent().find(".color_hover_text").css("display","none");
                jQuery(objCol).parent().parent().find(".color_hover_hover").css("display","none");
            }else if( jQuery(this).parent().parent().hasClass("color_hover_hover") ){
                alert(3);
                jQuery(objCol).parent().parent().find(".color_hover_text").css("display","none");
                jQuery(objCol).parent().parent().find(".color_hover_color").css("display","none");
            }  
        }else{
            alert(4);
            jQuery(objCol).parent().parent().find(".color_hover_text").css("display","table-cell");
            jQuery(objCol).parent().parent().find(".color_hover_color").css("display","table-cell");
            jQuery(objCol).parent().parent().find(".color_hover_hover").css("display","table-cell");
        }

        jQuery(".wp-picker-holder").css({
            'overflow':'inherit',
            'margin-bottom': '20px'
        })
    });*/

    //from the href="#" div[class^='apple-']
    /*jQuery('.ilentheme-options a[class^="ui-tabs-"],.ilenplugin-options a[class^="ui-tabs-"],footer a').on('click',function(event){
        //do something
        //prevent the click as is passed to the function as an event
        event.preventDefault(event);
        event.stopPropagation();
        //event.preventDefault(event);
        //return false;
        //var id=event.target;
        //alert(id);
        return false;
    });*/



    function ilenvalidatorsubmit(){
        var $ = jQuery;



        // validate Select2 Multiple
        var myselect2_before = $("._select2_mulpliple");
        var myselect2 = myselect2_before.next();
        if( myselect2_before.length ){

            /** 
            * @link http://jsfiddle.net/DYpU8/4/
            **/
            $( myselect2_before ).each(function() {
                var newselect2 = $(this).next();
                var select2parent = $(this).parent();
                // 'data' brings the unordered list, while val does not
                var data = newselect2.select2('data');
                
                // Push each item into an array
                var finalResult = [];
                for( item in newselect2.select2('data') ) {
                    finalResult.push(data[item].id);
                };
                
                // Display the result with a comma
                $(this).parent().find('._input_hidden_select2').val( finalResult.join(',') );

            });

        }

    }
         

});


// link: http://jsfiddle.net/67XDq/7/
function IF_textCounter(field, cnt, maxlimit) {         
    var cntfield = document.getElementById(cnt) 
     if (field.value.length > maxlimit) // if too long...trim it!
        field.value = field.value.substring(0, maxlimit);
        // otherwise, update 'characters left' counter
        else
        cntfield.value = maxlimit - field.value.length;
}
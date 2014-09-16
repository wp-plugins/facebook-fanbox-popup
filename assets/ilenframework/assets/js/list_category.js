jQuery(document).ready(function ($) {

	$(".component_list_categories .check_all").on("change",function(){

		if( $(this).prop('checked') ){

			$(".component_list_categories .checked_hidden").css("display","none");

		}else{

			$(".component_list_categories .checked_hidden").css("display","block");		

		}

	});

});

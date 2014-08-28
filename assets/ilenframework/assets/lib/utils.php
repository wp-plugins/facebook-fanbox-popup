<?php
/**
 * Pack Functions and Utils
 * iLenFramework 
 * @package ilentheme
 */

/* FUNCTION GET IMAGE POST */
    /*
    // Thumbnail (default 150px x 150px max)
    // Medium resolution (default 300px x 300px max)
    // Large resolution (default 640px x 640px max)
    // Full resolution (original size uploaded)
    */


    if ( ! function_exists( 'IF_get_image' ) ) {
      function get_ALTImage($ID){
          //code 
          return get_post_meta( $ID  , '_wp_attachment_image_alt', true);
      }
    }
    /* get image for src image in post // get original size  */
    if ( ! function_exists( 'IF_catch_that_image' ) ) {
        function IF_catch_that_image() {

          global $post, $posts, $options_my_plugin;
          $first_img = array();
          $matches = array();
          $output = '';

          ob_start();
          ob_end_clean();
          $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);

          if( isset($matches[1][0]) ){
            $first_img['src'] = $matches[1][0];
          }

          $first_img['alt'] = '';

          return $first_img;

        }
    }

    /* get featured image */
    if ( ! function_exists( 'IF_get_featured_image' ) ) {
        function IF_get_featured_image( $size = "medium" ){
            //code 
            global $post;
            $url = array();
            if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $size);

                $url['alt'] = get_ALTImage($post->ID);
                $url['src'] = $thumb['0'];
                return $url; 
            }else{
                return false;
            }
        }
    }

    /* get attachment image */
    if ( ! function_exists( 'IF_get_image_post_attachment' ) ) {
        function IF_get_image_post_attachment( $size = "medium" ){
            //code 
            global $post;
            $image = array();
            $args = array(
               'post_type' => 'attachment',
               'numberposts' => -1,
               'post_status' => null,
               'post_parent' => $post->ID
            );
            $image['alt'] = get_ALTImage($post->ID);
            $attachments = get_posts( $args );
            if ( $attachments ) {
                foreach ( $attachments as $attachment ) {
                   $_array_img = wp_get_attachment_image_src( $attachment->ID , $size );
                   $image['src']=$_array_img[0];
                   return $image;
                }
            }

            return false;
        }
    }


    /* get default imagen */
    if ( ! function_exists( 'IF_get_image_default2' ) ) {
        function IF_get_image_default2( $default_src="" ){

            $image = array();
            $image['alt']='';
            $image['src']= $default_src;

            return $image;

        }
    }



    if ( ! function_exists( 'IF_get_image' ) ) {
        function IF_get_image( $size = 'medium', $default='') {

            $img = IF_get_featured_image($size);
            if( isset($img['src']) ){
                return $img;
            }

            $img = IF_get_image_post_attachment($size);
            if( isset($img['src']) ){
                return $img;
            }
            $img = IF_catch_that_image();
            if( isset($img['src']) ){
                return $img;
            }else{

                return IF_get_image_default2( $default );

            }
        }
    }

   
/* END FUNCTION GET IMAGE POST */



// Class paginate [experimental]
if ( ! function_exists( 'IF_paginate' ) ) {
function  IF_paginate($total_rows,
                   $pagego,
                   $pagina,
                   $Nrecords=10,
                   $pagevar="pag",
                   $lang = ''){ 
                   
        $targetpage = "$pagego";
        $limit = $Nrecords; // TOTAL REGISTRATION PER PAGE
        $stages = 3;

        // Initial pagina num setup
        if ($pagina == 0 || !$pagina){$pagina = 1;}
        $prev = $pagina - 1;    
        $next = $pagina + 1;                            
        $lastpagina = ceil($total_rows/$limit);      
        $Lastpaginam1 = $lastpagina - 1;                    

        $paginate = '';

        if($lastpagina > 1)
        {
            $paginate .= "<div class='paginate'>";
            // Previous
            if ($pagina > 1){
                $paginate.= "<a class='' href='$targetpage&$pagevar=$prev'>".__('Previous',$lang)."</a>";
            }else{
                $paginate.= "<span class=' disabled'>".__('Previous',$lang)."</span>";   }

            // paginas  
            if ($lastpagina < 7 + ($stages * 2))    // Not enough paginas to breaking it up
            {
                for ($counter = 1; $counter <= $lastpagina; $counter++)
                {
                    if ($counter == $pagina){
                        $paginate.= "<span class='current'>$counter</span>";
                    }else{
                        $paginate.= "<a class='' href='$targetpage&$pagevar=$counter'>$counter</a>";}                   
                }
            }
            elseif($lastpagina > 5 + ($stages * 2)) // Enough paginas to hide a few?
            {
                // Beginning only hide later paginas
                if($pagina < 1 + ($stages * 2))     
                {
                    for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
                    {
                        if ($counter == $pagina){
                            $paginate.= "<span class='current'>$counter</span>";
                        }else{
                            $paginate.= "<a  class='' href='$targetpage&$pagevar=$counter'>$counter</a>";}                  
                    }
                    $paginate.= "<a href='#'>....</a>";
                    $paginate.= "<a  class='' href='$targetpage&$pagevar=$Lastpaginam1'>$Lastpaginam1</a>";
                    $paginate.= "<a  class='' href='$targetpage&$pagevar=$lastpagina'>$lastpagina</a>";     
                }
                // Middle hide some front and some back
                elseif($lastpagina - ($stages * 2) > $pagina && $pagina > ($stages * 2))
                {
                    $paginate.= "<a class='' href='$targetpage&$pagevar=1'>1</a>";
                    $paginate.= "<a class='' href='$targetpage&$pagevar=2'>2</a>";
                    $paginate.= "<a href='#'>....</a>";
                    for ($counter = $pagina - $stages; $counter <= $pagina + $stages; $counter++)
                    {
                        if ($counter == $pagina){
                            $paginate.= "<span class='current'>$counter</span>";
                        }else{
                            $paginate.= "<a  class='' href='$targetpage&$pagevar=$counter'>$counter</a>";
                        }                   
                    }
                    $paginate.= "<a href='#'>....</a>";
                    $paginate.= "<a  class='' href='$targetpage&$pagevar=$Lastpaginam1'>$Lastpaginam1</a>";
                    $paginate.= "<a  class='' href='$targetpage&$pagevar=$lastpagina'>$lastpagina</a>";     
                }
                // End only hide early paginas
                else
                {
                    $paginate.= "<a class='' href='$targetpage&$pagevar=1'>1</a>";
                    $paginate.= "<a class='' href='$targetpage&$pagevar=2'>2</a>";
                    $paginate.= "<a href='#'>....</a>";
                    for ($counter = $lastpagina - (2 + ($stages * 2)); $counter <= $lastpagina; $counter++)
                    {
                        if ($counter == $pagina){
                            $paginate.= "<span class='' class='current'>$counter</span>";
                        }else{
                            $paginate.= "<a class='' href='$targetpage&$pagevar=$counter'>$counter</a>";}                   
                    }
                }
            }
                        
                    // Next
            if ($pagina < $counter - 1){
                $paginate.= "<a class='' href='$targetpage&$pagevar=$next'>".__('Next',$lang)."</a>";
            }else{
                $paginate.= "<span class=' disabled' >".__('Next',$lang)."</span>";
                }
            // calculo
            $desc_text_end = $pagina * $Nrecords;
            $desc_text_begin = $desc_text_end - $Nrecords;
            $desc_text_begin = ($desc_text_begin==0)?1:$desc_text_begin;

            $paginate .="</div>";
       }
     // pagination
      return $paginate;

     //FIN PAGINACION // PAGINACION *******************************************************************************************************
}
}



if ( ! function_exists( 'IF_get_option' ) ) {
function IF_get_option( $subject ){

    $new_array = array();
    $new_data = get_option( $subject."_options" );
    if( is_array( $new_data ) ){
        foreach ($new_data as $key => $value) {
            $new_array[ str_replace($subject.'_', '', $key) ] = $value;
        }
    }

    return json_decode (json_encode ($new_array), FALSE);    
    
}
}




if ( ! function_exists( 'IF_getyoutubeID' ) ) {
function IF_getyoutubeID( $url ){

    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);    

    return (string)$matches[1];

}
}


if ( ! function_exists( 'IF_getyoutubeThumbnail' ) ) {

    /*
    *  @see http://stackoverflow.com/posts/2068371/revisions
    */ 

function IF_getyoutubeThumbnail( $id_youtube ){
    

    return "http://img.youtube.com/vi/$id_youtube/hqdefault.jpg";
    
}
}



?>
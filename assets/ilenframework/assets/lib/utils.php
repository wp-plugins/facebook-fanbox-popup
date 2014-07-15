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
    if ( ! function_exists( 'IF_get_image_default' ) ) {
        function IF_get_image_default(){
            $image = array();
            $image['alt']='';
            $image['src']=get_template_directory_uri() . '/framework/images/default.png';
            return $image;

        }
    }

    if ( ! function_exists( 'IF_get_image' ) ) {
        function IF_get_image( $size = "medium" ){

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

                return IF_get_image_default();

            }
        }
    }

   
/* END FUNCTION GET IMAGE POST */






?>
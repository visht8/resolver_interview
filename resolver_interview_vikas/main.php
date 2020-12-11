<?php

/*

Plugin Name: Vikas Assignment

Plugin URI: https://vikasthakur.coveysys.com

description: >-

call posts from any API end point

Version: 1.0

Author: Vikas Thakur

Author URI: https://vikasthakur.coveysys.com

License: GPL2

*/
//-------------------Enqueue CSS and JS for plugin use-----------------------------
function my_scripts() {
    wp_enqueue_style('bootstrap4', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script( 'boot1','https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', array( 'jquery' ),'',true );
    wp_enqueue_script( 'boot2','https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js', array( 'jquery' ),'',true );
    wp_enqueue_script( 'boot3','https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array( 'jquery' ),'',true );
}
add_action( 'wp_enqueue_scripts', 'my_scripts' ); 



add_action( 'wp_enqueue_scripts', 'so_enqueue_scripts' );
function so_enqueue_scripts(){
  wp_register_script( 
    'ajaxHandle', 
    plugins_url('js/ajaxloadpost.js', __FILE__), 
    array(), 
    false, 
    true 
  );
  wp_enqueue_script( 'ajaxHandle' );
  wp_localize_script( 
    'ajaxHandle', 
    'ajax_object', 
    array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) 
  );
}
//-------------------END OF enqueue CSS and JS -----------------------------



//-------------------Handle First load of all post from any API end point-----------------------------


function resolver_function($atts){
    
    extract(shortcode_atts(array(
        'source' => '',
     ), $atts)); 
  
     //$url = $source."/?page=1";   
     //$response = wp_remote_get( $source );
     //echo $source ;
     $response = wp_remote_get( esc_url_raw( $source ) );
     $api_response = json_decode( wp_remote_retrieve_body( $response ), true ); 
     $arr = $api_response['data'] ;  
     $pg_number = $api_response['meta']['pagination']['page'] + 1;
     $new_source = explode('/posts', $source);
     $new_source = $new_source[0].'/posts/?page='.$pg_number ;
     //echo '<pre>';
     //print_r ($arr) ;
 
     $return_string = '<div class="container"><div class="row" id="more-data">';
     foreach($arr as $item) {
         
       $return_string .= '
       <div class="col-md-4">
       <div class="caption">
       <strong>'.$item['title'].'</strong>
       </div>
       <p>'.$item['body'].'</p>
       </div>';
 
     }

     $return_string .= '<button type="button" value="'.$new_source.'" id="btnremove" class="btn-lg btn-block  btn-success ld-more ">Load More</button>';
     $return_string .= '</div></div>';
     
     
    // echo $return_string ; die();
     return $return_string;
  }
 

//-------------------ADD shortcode for plugin call-----------------------------


  function register_shortcodes(){
     add_shortcode('resolver_interview', 'resolver_function');
  }
 
  add_action( 'init', 'register_shortcodes');
//-------------------END of  shortcode for plugin call-----------------------------





//-------------------END of Handle First load of all post from any API end point-----------------------------



//-------------------AJAX call for load more event on button click ------------------------------------------

add_action( "wp_ajax_resolver", "so_wp_ajax_function" );
add_action( "wp_ajax_nopriv_resolver", "so_wp_ajax_function" );
function so_wp_ajax_function(){

  $source= $_POST['source'] ;
  $response = wp_remote_get( esc_url_raw( $source ) );
  $api_response = json_decode( wp_remote_retrieve_body( $response ), true ); 
  
  $pg_number = $api_response['meta']['pagination']['page'] + 1 ;
  $new_source = explode('/posts', $source);
  $new_source = $new_source[0].'/posts/?page='.$pg_number ;
  
  $arr = $api_response['data'] ;
  $return_string = '';
  foreach($arr as $item) {
      
    $return_string .= '
    <div class="col-md-4">
    <div class="caption">
    <strong>'.$item['title'].'</strong>
    </div>
    <p>'.$item['body'].'</p>
    </div>';    
  }

  $return_string .= '<button type="button" value="'.$new_source.'" id="btnremove" class="btn-lg btn-block  btn-success ld-more ">Load More</button>';

 echo $return_string ;    
 // return $return_string;
 wp_die(); // ajax call must die to avoid trailing 0 in your response
}


//-------------------END of AJAX call for load more event on button click ------------------------------------------



?>
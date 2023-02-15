<?php
/**
 * Template Name1: Testing
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 16/12/15
 * Time: 3:27 PM
 */
get_header();

/*$ne_lat = 25.836082521441075;
$ne_lng = -80.13639138691404;
$sw_lat = 25.74922007145464;
$sw_lng = -80.36298440449217;*/

/*$ne_lat = 25.84011483252356;
$ne_lng = -80.20923644609374;
$sw_lat = 25.75325533806983;
$sw_lng = -80.43582946367187;*/

/*$ne_lat = 25.848302922645377;
$ne_lng = -79.99431640214843;
$sw_lat = 25.744442191972414;
$sw_lng = -80.22090941972655;*/

/*$ne_lat = 25.857417299617648;
$ne_lng = -79.95895415849608;
$sw_lat = 25.75356455939229;
$sw_lng = -80.18554717607421;*/

/*$ne_lat = 25.80859312254879;
$ne_lng = -80.06366759843749;
$sw_lat = 25.704697609558625;
$sw_lng = -80.29026061601562;*/


/*$ne_lat = 25.787883120226418;
$ne_lng = -80.01766234941405;
$sw_lat = 25.683969486908396;
$sw_lng = -80.24425536699218;*/

$ne_lat = '25.881821831399073';
$ne_lng = '-80.03860503740233';
$sw_lat = '25.781700328799232';
$sw_lng = '-80.26519805498046';

//25.8101385,-80.2009967,14

/*$latitude = 25.790654;
$longitude = -80.1300455;
$radius = 11; // in miles

$radius_unit = houzez_option('radius_unit');
if( $radius_unit == 'km' ) {
    $earth_radius_num = 111;
} elseif ( $radius_unit == 'mi' ) {
    $earth_radius_num = 69;
} else {
    $earth_radius_num = 111;
}*/


$args = array(
   'post_type' => 'property',
   'posts_per_page' => -1,
   'post_status' => 'publish',
   'meta_query' => array(
      array(
        'key' => 'houzez_geolocation_lat',
        'value' => array($sw_lat, $ne_lat),
        'type' => 'CHAR',
        'compare' => 'BETWEEN'
      ),
      array(
        'key' => 'houzez_geolocation_long',
        'value' => array($ne_lng, $sw_lng),
        'type' => 'CHAR',
        'compare' => 'BETWEEN'
      )
    )
);

echo '<pre>';
print_r($args);

$query = new WP_Query( $args );

if ( $query->have_posts() ) {
   while ( $query->have_posts() ) {
       $query->the_post();
       
       echo '<a href="'.get_permalink().'">';
       the_title(); 
       echo '</a>';
       echo '<br>';
   }
   wp_reset_postdata();
} else {
   // No posts found
}


get_footer(); ?>
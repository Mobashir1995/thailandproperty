<?php
$theme = wp_get_theme();
define('HOUZEZZ_CHILD_VERSION', $theme['Version']);

add_action( 'elementor/widgets/widgets_registered', 'pd_register_widgets' );
function pd_register_widgets(){
    // code will goes here
    require_once( get_stylesheet_directory() . '/elementor/property-card-v6.php' );
    require_once( get_stylesheet_directory() . '/elementor/property-card-v1.php' );
    require_once( get_stylesheet_directory() . '/elementor/booking-map-widget.php' );
    require_once( get_stylesheet_directory() . '/elementor/property-carousel-v1.php' );
}

/*-----------------------------------------------------------------------------------*/
// Register Scripts
/*-----------------------------------------------------------------------------------*/
function houzez_child_enqueue_scripts(){
    if ( is_page_template('template/user_dashboard_submit.php') ) {
        wp_enqueue_script('houzezz-child-dashboard-submit', get_stylesheet_directory_uri() . '/js/dashboard-user-submit.js', array('jquery'), HOUZEZZ_CHILD_VERSION, true);
    }
}
add_action( 'wp_enqueue_scripts', 'houzez_child_enqueue_scripts' );

/*-----------------------------------------------------------------------------------*/
// Generate Hirarchical terms
/*-----------------------------------------------------------------------------------*/
if(!function_exists('houzez_hirarchical_options')){
    function houzez_hirarchical_options($taxonomy_name, $taxonomy_terms, $searched_term, $prefix = " " ){

        if (!empty($taxonomy_terms) && taxonomy_exists($taxonomy_name)) {
            foreach ($taxonomy_terms as $term) {

                if( $taxonomy_name == 'property_area' ) {
                    $term_meta= get_option( "_houzez_property_area_$term->term_id");
                    $parent_city = sanitize_title($term_meta['parent_city']);

                    if ( class_exists( 'sitepress' ) ) {
                        $default_lang = apply_filters( 'wpml_default_language', NULL );
                        $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_area', true, $default_lang );
                        $term_meta= get_option( "_houzez_property_area_$term_id_default");
                        $parent_city = sanitize_title($term_meta['parent_city']);
                        $parent_city = get_term_by( 'slug', $parent_city, 'property_city' )->slug;
                    }

                    $lat = '';
                    $long = '';
                    if( function_exists('get_field') ){
                        $lat = get_field('latitude', 'term_'.$term->term_id);
                        $long = get_field('longitude', 'term_'.$term->term_id);
                    }

                    if ($searched_term == $term->slug) {
                        echo '<option data-lat="'.esc_attr($lat).'" data-long="'.esc_attr($long).'" data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_city).'" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-lat="'.esc_attr($lat).'" data-long="'.esc_attr($long).'" data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_city).'" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }
                    
                } elseif( $taxonomy_name == 'property_city' ) {
                    $term_meta= get_option( "_houzez_property_city_$term->term_id");
                    $parent_state = sanitize_title($term_meta['parent_state']);

                    if ( class_exists( 'sitepress' ) ) {
                        $default_lang = apply_filters( 'wpml_default_language', NULL );
                        $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_city', true, $default_lang );
                        $term_meta= get_option( "_houzez_property_city_$term_id_default");
                        $parent_state = sanitize_title($term_meta['parent_state']);
                        $parent_state = get_term_by( 'slug', $parent_state, 'property_state' )->slug;
                    }

                    if ($searched_term == $term->slug) {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_state).'" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_state).'" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                } elseif( $taxonomy_name == 'property_state' ) {

                    $term_meta = get_option( "_houzez_property_state_$term->term_id");
                    $parent_country = sanitize_title($term_meta['parent_country']);

                    if ( class_exists( 'sitepress' ) ) {
                        $default_lang = apply_filters( 'wpml_default_language', NULL );
                        $term_id_default = apply_filters( 'wpml_object_id', $term->term_id, 'property_state', true, $default_lang );
                        $term_meta= get_option( "_houzez_property_state_$term_id_default");
                        $parent_country = sanitize_title($term_meta['parent_country']);
                        $parent_country = get_term_by( 'slug', $parent_country, 'property_country' )->slug;
                    }

                    if ($searched_term == $term->slug) {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_country).'" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_country).'" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                } elseif( $taxonomy_name == 'property_country' ) {
            
                    if ($searched_term == $term->slug) {
                        echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                } else {

                    if ($searched_term == $term->slug) {
                        echo '<option value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    }
                }


                $child_terms = get_terms($taxonomy_name, array(
                    'hide_empty' => false,
                    'parent' => $term->term_id
                ));

                if (!empty($child_terms)) {
                    houzez_hirarchical_options( $taxonomy_name, $child_terms, $searched_term, "- ".$prefix );
                }
            }
        }
    }
}

if(!function_exists('houzez_taxonomy_hirarchical_options_for_search')){
    function houzez_taxonomy_hirarchical_options_for_search($taxonomy_name, $taxonomy_terms, $target_term_name, $prefix = " " ){
        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {

                if( $taxonomy_name == 'property_area' ) {
                    $term_meta= get_option( "_houzez_property_area_$term->term_id");
                    $parent_city = sanitize_title($term_meta['parent_city']);

                    $lat = '';
                    $long = '';
                    if( function_exists('get_field') ){
                        $lat = get_field('latitude', 'term_'.$term->term_id);
                        $long = get_field('longitude', 'term_'.$term->term_id);
                    }
                    
                    if ($target_term_name == $term->slug) {
                        echo '<option data-lat="'.esc_attr($lat).'" data-long="'.esc_attr($long).'" data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_city).'" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-lat="'.esc_attr($lat).'" data-long="'.esc_attr($long).'" data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_city).'" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                } elseif( $taxonomy_name == 'property_city' ) {
                    $term_meta= get_option( "_houzez_property_city_$term->term_id");
                    $parent_state = sanitize_title($term_meta['parent_state']);

                    if ($target_term_name == $term->slug) {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_state).'" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_state).'" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                }  elseif( $taxonomy_name == 'property_state' ) {
                    $term_meta= get_option( "_houzez_property_state_$term->term_id");
                    $parent_country = sanitize_title($term_meta['parent_country']);

                    if ($target_term_name == $term->slug) {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_country).'" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-ref="' . urldecode($term->slug) . '" data-belong="'.urldecode($parent_country).'" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                } elseif( $taxonomy_name == 'property_country' ) {
            
                    if ($target_term_name == $term->slug) {
                        echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '" selected="selected">' . esc_attr($prefix) . esc_attr($term->name) . '</option>';
                    } else {
                        echo '<option data-ref="' . urldecode($term->slug) . '" value="' . urldecode($term->slug) . '">' . esc_attr($prefix) . esc_attr($term->name) .'</option>';
                    }

                } else {
                    if ($target_term_name == $term->slug) {
                        echo '<option value="' . urldecode($term->slug) . '" selected="selected">' . $prefix . $term->name . '</option>';
                    } else {
                        echo '<option value="' . urldecode($term->slug) . '">' . $prefix . $term->name . '</option>';
                    }
                }


                $child_terms = get_terms($taxonomy_name, array(
                    'hide_empty' => false,
                    'parent' => $term->term_id
                ));

                if (!empty($child_terms)) {
                    houzez_taxonomy_hirarchical_options_for_search( $taxonomy_name, $child_terms, $target_term_name, "- ".$prefix );
                }
            }
        }
    }
}

require_once get_stylesheet_directory() . '/inc/helper-functions.php';
require_once get_stylesheet_directory() . '/inc/property-functions.php';
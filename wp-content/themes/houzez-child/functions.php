<?php
add_action( 'elementor/widgets/widgets_registered', 'pd_register_widgets' );
function pd_register_widgets(){
    // code will goes here
    require_once( get_stylesheet_directory() . '/elementor/property-card-v6.php' );
    require_once( get_stylesheet_directory() . '/elementor/property-card-v1.php' );
    require_once( get_stylesheet_directory() . '/elementor/booking-map-widget.php' );
}

/*-----------------------------------------------------------------------------------*/
// Generate Hirarchical terms
/*-----------------------------------------------------------------------------------*/
if(!function_exists('houzez_get_search_taxonomies')){
    function houzez_get_search_taxonomies($taxonomy_name, $searched_data = "", $args = array() ){
         
        $hide_empty = false;
        if($taxonomy_name == 'property_city' || $taxonomy_name == 'property_area' || $taxonomy_name == 'property_country' || $taxonomy_name == 'property_state') {
            $hide_empty = houzez_hide_empty_taxonomies();
        }
         
        $defaults = array(
            'taxonomy' => $taxonomy_name,
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => $hide_empty,
        );
 
        $args       = wp_parse_args( $args, $defaults );
        $taxonomies = get_terms( $args );
 
        if ( empty( $taxonomies ) || is_wp_error( $taxonomies ) ) {
            return false;
        }
 
        $output = '';
        foreach( $taxonomies as $category ) {
            if( $category->parent == 0 ) {
 
                $data_attr = $data_subtext = '';
 
                if( $taxonomy_name == 'property_city' ) {
                    $term_meta= get_option( "_houzez_property_city_$category->term_id");
                    $parent_state = isset($term_meta['parent_state']) ? $term_meta['parent_state'] : '';
                    $parent_state = sanitize_title($parent_state);
 
                    // WPML workaround for compsupp-6550 | Part 1
                    if ( class_exists( 'sitepress' ) ) {                        
                        // Configs
                        $default_lang = apply_filters('wpml_default_language', NULL );
                        $current_lang = apply_filters( 'wpml_current_language', NULL );
                        $id = $category->term_id;
                        $tax_slug = $taxonomy_name;                        
                         
                        if ($default_lang != $current_lang ) {
                            $term_id_default = apply_filters( 'wpml_object_id', $id, $tax_slug, true, $default_lang );
                            $term_meta= get_option( "_houzez_".$tax_slug."_".$term_id_default);
 
                            // Replace with the correct settings
                            $parent_state = sanitize_title($term_meta['parent_state']);
                            $parent_state = get_term_by( 'slug', $parent_state, 'property_state' )->slug;
                        }
                    }
 
                    $data_attr = 'data-belong="'.esc_attr($parent_state).'"';
                    $data_subtext = 'data-subtext="'.houzez_get_term_name_by_slug($parent_state, 'property_state').'"';
 
                } elseif( $taxonomy_name == 'property_area' ) {
                    $term_meta= get_option( "_houzez_property_area_$category->term_id");
                    $parent_city = isset($term_meta['parent_city']) ? $term_meta['parent_city'] : '';
                    $parent_city = sanitize_title($parent_city);
                     
                    // WPML workaround for compsupp-6550 | Part 2
                    if ( class_exists( 'sitepress' ) ) {                      
                        // Configs
                        $default_lang = apply_filters('wpml_default_language', NULL );
                        $current_lang = apply_filters( 'wpml_current_language', NULL );
                        $id = $category->term_id;
                        $tax_slug = $taxonomy_name;                        
                         
                        if ($default_lang != $current_lang ) {
                            $term_id_default = apply_filters( 'wpml_object_id', $id, $tax_slug, true, $default_lang );
                            $term_meta= get_option( "_houzez_".$tax_slug."_".$term_id_default);
 
                            // Replace with the correct settings
                            $parent_city = sanitize_title($term_meta['parent_city']);
                            $parent_city = get_term_by( 'slug', $parent_city, 'property_city' )->slug;
                        }
                    }
 
                     
 
                    $data_attr = 'data-belong="'.esc_attr($parent_city).'"';
                    $data_subtext = 'data-subtext="'.houzez_get_term_name_by_slug($parent_city, 'property_city').'"';
 
                } elseif( $taxonomy_name == 'property_state' ) {
                    $term_meta = get_option( "_houzez_property_state_$category->term_id");
                    $parent_country = isset($term_meta['parent_country']) ? $term_meta['parent_country'] : '';
                    $parent_country = sanitize_title($parent_country);
                    $data_attr = 'data-belong="'.esc_attr($parent_country).'"';
                    $data_subtext = 'data-subtext="'.houzez_get_term_name_by_slug($parent_country, 'property_country').'"';
 
                    // WPML workaround for compsupp-6550 | Part 3
                    if ( class_exists( 'sitepress' ) ) {                      
                        // Configs
                        $default_lang = apply_filters('wpml_default_language', NULL );
                        $current_lang = apply_filters( 'wpml_current_language', NULL );
                        $id = $category->term_id;
                        $tax_slug = $taxonomy_name;                        
                         
                        if ($default_lang != $current_lang ) {
                            $term_id_default = apply_filters( 'wpml_object_id', $id, $tax_slug, true, $default_lang );
                            $term_meta= get_option( "_houzez_".$tax_slug."_".$term_id_default);
 
                            // Replace with the correct settings
                            $parent_country = sanitize_title($term_meta['parent_country']);
                            $parent_country = get_term_by( 'slug', $parent_country, 'property_country' )->slug;
                        }
                    }
 
                }
 
                if ( !empty($searched_data) && in_array( $category->slug, $searched_data ) ) {
                    $output.= '<option data-ref="'.esc_attr($category->slug).'" '.$data_attr.' '.$data_subtext.' value="' . esc_attr($category->slug) . '" selected="selected">'. esc_attr($category->name) . '</option>';
                } else {
                    $output.= '<option data-ref="'.esc_attr($category->slug).'" '.$data_attr.' '.$data_subtext.' value="' . esc_attr($category->slug) . '">' . esc_attr($category->name) . '</option>';
                }
 
                foreach( $taxonomies as $subcategory ) {
                    if($subcategory->parent == $category->term_id) {
 
                        $data_attr_child = '';
                        if( $taxonomy_name == 'property_city' ) {
                            $term_meta= get_option( "_houzez_property_city_$subcategory->term_id");
                            $parent_state = isset($term_meta['parent_state']) ? $term_meta['parent_state'] : '';
                            $parent_state = sanitize_title($parent_state);
                            $data_attr_child = 'data-belong="'.esc_attr($parent_state).'"';
 
                        } elseif( $taxonomy_name == 'property_area' ) {
                            $term_meta= get_option( "_houzez_property_area_$subcategory->term_id");
                            $parent_city = isset($term_meta['parent_city']) ? $term_meta['parent_city'] : '';
                            $parent_city = sanitize_title($parent_city);
                            $data_attr_child = 'data-belong="'.esc_attr($parent_city).'"';
 
                        } elseif( $taxonomy_name == 'property_state' ) {
                            $term_meta= get_option( "_houzez_property_state_$subcategory->term_id");
                            $parent_country = isset($term_meta['parent_country']) ? $term_meta['parent_country'] : '';
                            $parent_country = sanitize_title($parent_country);
                            $data_attr_child = 'data-belong="'.esc_attr($parent_country).'"';
                        }
 
                        if ( !empty($searched_data) && in_array( $subcategory->slug, $searched_data ) ) {
                            $output.= '<option data-ref="'.esc_attr($subcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subcategory->slug) . '" selected="selected"> - '. esc_attr($subcategory->name) . '</option>';
                        } else {
                            $output.= '<option data-ref="'.esc_attr($subcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subcategory->slug) . '"> - ' . esc_attr($subcategory->name) . '</option>';
                        }
 
                        foreach( $taxonomies as $subsubcategory ) {
                            if($subsubcategory->parent == $subcategory->term_id) {
 
                                $data_attr_child = '';
                                if( $taxonomy_name == 'property_city' ) {
                                    $term_meta= get_option( "_houzez_property_city_$subsubcategory->term_id");
                                    $parent_state = isset($term_meta['parent_state']) ? $term_meta['parent_state'] : '';
                                    $parent_state = sanitize_title($parent_state);
                                    $data_attr_child = 'data-belong="'.esc_attr($parent_state).'"';
 
                                } elseif( $taxonomy_name == 'property_area' ) {
                                    $term_meta= get_option( "_houzez_property_area_$subsubcategory->term_id");
                                    $parent_city = isset($term_meta['parent_city']) ? $term_meta['parent_city'] : '';
                                    $parent_city = sanitize_title($parent_city);
                                    $data_attr_child = 'data-belong="'.esc_attr($parent_city).'"';
 
                                } elseif( $taxonomy_name == 'property_state' ) {
                                    $term_meta= get_option( "_houzez_property_state_$subsubcategory->term_id");
                                    $parent_country = isset($term_meta['parent_country']) ? $term_meta['parent_country'] : '';
                                    $parent_country = sanitize_title($parent_country);
                                    $data_attr_child = 'data-belong="'.esc_attr($parent_country).'"';
                                }
 
                                if ( !empty($searched_data) && in_array( $subsubcategory->slug, $searched_data ) ) {
                                    $output.= '<option data-ref="'.esc_attr($subsubcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subsubcategory->slug) . '" selected="selected"> - '. esc_attr($subsubcategory->name) . '</option>';
                                } else {
                                    $output.= '<option data-ref="'.esc_attr($subsubcategory->slug).'" '.$data_attr_child.' value="' . esc_attr($subsubcategory->slug) . '"> -- ' . esc_attr($subsubcategory->name) . '</option>';
                                }
                            }
                        }
                    }
                }
            }
        }
        echo $output;
 
    }
}
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
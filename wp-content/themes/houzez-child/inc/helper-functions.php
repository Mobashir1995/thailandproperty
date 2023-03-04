<?php
/*-----------------------------------------------------------------------------------*/
// get taxonomies with with id value as button
/*-----------------------------------------------------------------------------------*/
if(!function_exists('houzez_taxonomy_id_by_postID')){
    function houzez_taxonomy_id_by_postID( $property_id, $taxonomy_name ){
        $tax_terms = get_the_terms( $property_id, $taxonomy_name );
        $term_id = 0;
        if( !empty($tax_terms) ){
            foreach( $tax_terms as $tax_term ){
                $term_id = $tax_term->term_id;
                break;
            }
        }
        return $term_id;
    }
}

/*-----------------------------------------------------------------------------------*/
// get taxonomies with with id value as button
/*-----------------------------------------------------------------------------------*/
if(!function_exists('houzez_get_taxonomies_with_id_value_as_radio')){
    function houzez_get_taxonomies_with_id_value_as_radio($taxonomy, $parent_taxonomy, $taxonomy_id, $prefix = " ", $type="radio", $input_name='' ){
        $name = '';
        if( $taxonomy === 'property_type' ){
            $name = 'name="prop_type[]"';
        }elseif( $taxonomy === 'property_status' ){
            $name = 'name="prop_status[]"';
        }else{
            $name = 'name="prop_labels[]"';
        }

        if( !empty($input_name) ){
            $name = 'name="'.$input_name.'[]"';
        }

        $input_type = 'type="radio"';
        if( $type == 'checkbox' ){
            $input_type = 'type="checkbox"';
        }
        
        if (!empty($parent_taxonomy)) {
            foreach ($parent_taxonomy as $term) {
                $admin_visibility = get_field('restrict_only_for_admin', $taxonomy.'_'.$term->term_id);
                if( $admin_visibility === 'yes' ){ continue; }
                echo '<span class="dashboard_prop_button_container '.$taxonomy.' '.$term->slug.'">';
                if ($taxonomy_id != $term->term_id) {
                    echo '<label><input '.$input_type.' data-slug="'.$term->slug.'" '.$name.'  required="required" value="' . $term->term_id . '"><span class="dashboard_prop_button btn btn-primary bg-violet bg-hover-black">' . $prefix . $term->name . '</span></label>';
                } else {
                    echo '<label><input '.$input_type.' data-slug="'.$term->slug.'" '.$name.' required="required" value="' . $term->term_id . '" checked="checked"><span class="dashboard_prop_button btn btn-primary bg-violet bg-hover-black">' . $prefix . $term->name . '</span></label>';
                }
                $get_child_terms = get_terms($taxonomy, array(
                    'hide_empty' => false,
                    'parent' => $term->term_id
                ));

                if (!empty($get_child_terms)) {
                    houzez_get_taxonomies_with_id_value_as_radio( $taxonomy, $get_child_terms, $taxonomy_id, " ".$prefix, $type, $input_name );
                }
                echo "</span>";
            }
        }
    }
}

/*-----------------------------------------------------------------------------------*/
// Submit Property Action
/*-----------------------------------------------------------------------------------*/
add_action('houzez_after_property_submit', 'houzez_child_submit_listing', 11, 1);
add_action('houzez_after_property_update', 'houzez_child_submit_listing', 11, 1);
function houzez_child_submit_listing($prop_id){
    // Add property type
    if( isset( $_POST['prop_type'] ) && ( $_POST['prop_type'] != '-1' ) ) {
        $type = array_map( 'intval', $_POST['prop_type'] );
        wp_set_object_terms( $prop_id, $type, 'property_type' );
    } else {
        wp_set_object_terms( $prop_id, '', 'property_type' );
    }

    $prop_type_labels = array();
    // Add property Type Labels
    if( isset( $_POST['prop_type_labels'] ) ) {
        $prop_type_labels = array_map( 'intval', $_POST['prop_type_labels'] );
    }

    // Add property status
    if( isset( $_POST['prop_status'] ) && ( $_POST['prop_status'] != '-1' ) ) {
        $prop_status = array_map( 'intval', $_POST['prop_status'] );
        wp_set_object_terms( $prop_id, $prop_status, 'property_status' );
    } else {
        wp_set_object_terms( $prop_id, '', 'property_status' );
    }

    $prop_status_labels = array();
    // Add property Status Labels
    if( isset( $_POST['prop_status_labels'] ) ) {
        $prop_status_labels = array_map( 'intval', $_POST['prop_status_labels'] );
    }

    $prop_labels = array();
    // Add property status
    if( isset( $_POST['prop_labels'] ) ) {
        $prop_labels = array_map( 'intval', $_POST['prop_labels'] );
    }

    $label_array = array_merge($prop_type_labels, $prop_status_labels, $prop_labels);
    if( is_array($label_array) && !empty($label_array) ){
        $label_array = array_unique($label_array);
        $label = wp_set_object_terms( $prop_id, $label_array, 'property_label' );
    }
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
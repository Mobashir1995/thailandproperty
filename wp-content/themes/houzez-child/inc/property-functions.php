<?php
if ( !function_exists( 'houzez_get_auto_complete_search' ) ) {

    function houzez_get_auto_complete_search() {
        $current_language = apply_filters( 'wpml_current_language', null );
        global $wpdb;
        $key = $_POST['key'];
        $key = $wpdb->esc_like($key);
        $keyword_field = houzez_option('keyword_field');
        $houzez_local = houzez_get_localization();
        $response = '';

        if( $keyword_field != 'prop_city_state_county' ) {

            if ( $keyword_field == "prop_title" ) {

                $table = $wpdb->posts;

                $data = $wpdb->get_results( 
                    $wpdb->prepare(
                        "SELECT DISTINCT * FROM {$table} WHERE post_type = %s AND post_status = %s AND (post_title LIKE %s OR post_content LIKE %s)",
                        'property',
                        'publish',
                        '%' . $key . '%',
                        '%' . $key . '%'
                    )
                );


                if ( sizeof( $data ) != 0 ) {

                    $search_url = add_query_arg( 'keyword', $key, houzez_get_search_template_link() );

                    echo '<div class="auto-complete-keyword">';
                    echo '<ul class="list-group">';

                    $new_data = array();

                    foreach ( $data as $post ) {

                        $propID = $post->ID;

                        $post_language = apply_filters( 'wpml_element_language_code', null, array('element_id' => $propID, 'element_type' => 'post'));

                        if ($post_language !== $current_language) {
                            continue;
                        }

                        $new_data [] = $post;
                        
                        // echo $prop_thumb = get_the_post_thumbnail( $propID );
                        $prop_beds = get_post_meta( $propID, 'fave_property_bedrooms', true );
                        $prop_baths = get_post_meta( $propID, 'fave_property_bathrooms', true );
                        $prop_size = houzez_get_listing_area_size( $propID );
                        $prop_type = houzez_taxonomy_simple('property_type');
                        $prop_img = get_the_post_thumbnail_url( $propID, array ( 40, 40 ) );

                        if ( empty( $prop_img ) ) {
                            $prop_img = houzez_get_image_placeholder_url('thumbnail');
                        }

                        ?>

                        <li class="list-group-item" data-text="<?php echo $post->post_title; ?>">
                            <div class="d-flex align-items-center">
                                <div class="auto-complete-image-wrap">
                                    <a href="<?php the_permalink( $propID ); ?>">
                                        <img class="img-fluid rounded" src="<?php echo $prop_img; ?>" width="40" height="40" alt="image">
                                    </a>    
                                </div><!-- auto-complete-image-wrap -->
                                <div class="auto-complete-content-wrap ml-3">
                                    <div class="auto-complete-title">
                                        <a href="<?php the_permalink( $propID ); ?>"><?php echo $post->post_title; ?></a>
                                    </div>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </li><!-- list-group-item -->
                        <?php

                    }

                    echo '</ul>';

                    echo '<div class="auto-complete-footer">';
                        echo '<span class="auto-complete-count"><i class="houzez-icon icon-pin mr-1"></i> ' . sizeof( $new_data ) . ' '.$houzez_local['listins_found'].'</span>';
                        echo '<a target="_blank" href="' . $search_url . '" class="search-result-view">'.$houzez_local['view_all_results'].'</a>';
                    echo '</div>';


                    echo '</div>';

                } else {

               ?>
               <ul class="list-group">
                   <li class="list-group-item"> <?php echo $houzez_local['auto_result_not_found']; ?> </li>
               </ul>
               <?php

           }

       } else if ( $keyword_field == "prop_address" ) {

                $posts_table = $wpdb->posts;
                $postmeta_table = $wpdb->postmeta;

                $data = $wpdb->get_results( 
                    $wpdb->prepare(
                            "SELECT DISTINCT post.ID, meta.meta_value FROM {$postmeta_table} AS meta INNER JOIN $posts_table AS post ON meta.post_id=post.ID AND post.post_type='property' and post.post_status='publish' AND meta.meta_value LIKE %s AND ( meta.meta_key='fave_property_map_address' OR meta.meta_key='fave_property_zip' OR meta.meta_key='fave_property_address' OR meta.meta_key='fave_property_id' )",
                            '%' . $key . '%'
                        )
                );

                if ( sizeof( $data ) != 0 ) {

                    echo '<ul class="list-group">';

                    $new_data = array();

                    foreach ( $data as $title ) {

                        $post_language = apply_filters( 'wpml_element_language_code', null, array('element_id' => $title->ID, 'element_type' => 'post'));

                        if ($post_language !== $current_language) {
                            continue;
                        }

                        $new_data [] = $title;
                        ?>
                        
                        <li class="list-group-item" data-text="<?php echo $title->meta_value; ?>">
                            <div class="d-flex align-items-center">
                                <div class="auto-complete-content-wrap flex-fill">
                                    <i class="houzez-icon icon-pin mr-1"></i> <?php echo $title->meta_value; ?>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </li>
                        <?php

                    }

                    echo '</ul>';

                } else {

               ?>
               <ul class="list-group">
                   <li class="list-group-item"> <?php echo $houzez_local['auto_result_not_found']; ?> </li>
               </ul>
               <?php

           }

            }

        } else {
            $terms_table = $wpdb->terms;
            $term_taxonomy = $wpdb->term_taxonomy;

            $data = $wpdb->get_results( 
                $wpdb->prepare(
                    "SELECT DISTINCT * FROM {$terms_table} as term INNER JOIN $term_taxonomy AS term_taxonomy ON term.term_id = term_taxonomy.term_id AND term.name LIKE %s AND ( term_taxonomy.taxonomy = %s OR term_taxonomy.taxonomy = %s OR term_taxonomy.taxonomy = %s )",
                    '%' . $key . '%',
                    'property_area',
                    'property_city',
                    'property_state'
                )
            );

            if ( sizeof( $data ) != 0 ) {

                echo '<ul class="list-group">';

                $new_data = array();

                foreach ( $data as $term ) {
                    
                    $term_language = apply_filters( 'wpml_element_language_code', null, array('element_id' => $term->term_id, 'element_type' => 'category'));

                    if ($term_language !== $current_language) {
                        continue;
                    }

                    $new_data [] = $term;

                    $taxonomy_img_id = get_term_meta( $term->term_id, 'fave_taxonomy_img', true );
                    $term_type = explode( 'property_', $term->taxonomy );
                    $term_type = $term_type[1];
                    $prop_count = $term->count;

                    $taxonomy_url = '';
                    if( $term->taxonomy === 'property_state' ){
                        $taxonomy_url = get_field('state_url', $term->taxonomy . '_' . $term->term_id);
                    }elseif( $term->taxonomy === 'property_city' ){
                        $taxonomy_url = get_field('city_url', $term->taxonomy . '_' . $term->term_id);
                    }elseif( $term->taxonomy === 'property_area' ){
                        $taxonomy_url = get_field('area_url', $term->taxonomy . '_' . $term->term_id);
                    }

                    if( !wp_http_validate_url( $taxonomy_url ) ) {
                        $taxonomy_url = get_term_link( $term );
                    }

                    if ( empty( $taxonomy_img_id ) ) {
                       $term_img = '<img src="http://placehold.it/40x40" width="40" height="40">';
                   } else {
                        $term_img = wp_get_attachment_image( $taxonomy_img_id, array( 40, 40 ), array( "class" => "img-fluid rounded" ) );
                   }

                    if ( $term_type == 'city' ) {
                        $term_type = $houzez_local['auto_city'];
                    } elseif ( $term_type == 'area' ) {
                        $term_type = $houzez_local['auto_area'];
                    } else {
                        $term_type = $houzez_local['auto_state'];
                    }

                    ?>
                    <li class="list-group-item" data-text="<?php echo $term->name; ?>">
                        <a href="<?php echo $taxonomy_url; ?>" >
                            <div class="d-flex align-items-center">
                                <div class="auto-complete-image-wrap">
                                    
                                        <?php echo $term_img; ?>
                                    
                                </div><!-- auto-complete-image-wrap -->
                                <div class="auto-complete-content-wrap flex-fill ml-3">
                                    <div class="auto-complete-title"><?php echo esc_attr($term->name); ?></div>
                                    <ul class="item-amenities">
                                        <li><?php if ( !empty( $term_type ) ) { ?>
                                        <?php echo $term_type; ?>
                                    <?php } ?>
                                    <?php if ( !empty( $prop_count ) ) : ?>
                                        - <?php echo $prop_count . ' ' . $houzez_local['auto_listings']; ?>
                                    <?php endif; ?></li>
                                    </ul>
                                </div><!-- auto-complete-content-wrap -->
                                <div class="auto-complete-content-wrap ml-3">
                                    <u class="search-result-view"><?php echo $houzez_local['auto_view_lists']; ?></u>
                                </div><!-- auto-complete-content-wrap -->
                            </div><!-- d-flex -->
                        </a>
                    </li>
                    <?php

                }

                echo '</ul>';

            } else {

               ?>
               <ul class="list-group">
                   <li class="list-group-item"> <?php echo $houzez_local['auto_result_not_found']; ?> </li>
               </ul>
               <?php

           }

        }

        wp_die();

    }

}
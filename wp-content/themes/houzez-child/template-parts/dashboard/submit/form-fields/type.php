<div class="form-group">
	<label for="prop_type">
		<?php echo houzez_option( 'cl_prop_type', 'Property Type' ) . houzez_required_field( 'prop_type' ); ?>		
	</label>
		
	<?php
		$property_types_terms = get_terms(
			array(
				'property_type',
			),
			array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
				'parent'     => 0,
			)
		);
		if ( houzez_edit_property() ) {
			global $property_data;
			$term_id = houzez_taxonomy_id_by_postID( $property_data->ID, 'property_type' );
			houzez_get_taxonomies_with_id_value_as_radio( 'property_type', $property_types_terms, $term_id );
		} else {
			houzez_get_taxonomies_with_id_value_as_radio( 'property_type', $property_types_terms, -1 );
		}
		?>

	
</div><!-- form-group -->

<div class="form-group">
	<label for="prop_status">
		<?php echo houzez_option( 'cl_prop_status', 'Property Status' ) . houzez_required_field( 'prop_status' ); ?>		
	</label>
		
	<?php
		$property_types_terms = get_terms(
			array(
				'property_status',
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
			$term_id = houzez_taxonomy_id_by_postID( $property_data->ID, 'property_status' );
			houzez_get_taxonomies_with_id_value_as_radio( 'property_status', $property_types_terms, $term_id );
		} else {
			houzez_get_taxonomies_with_id_value_as_radio( 'property_status', $property_types_terms, -1 );
		}
		?>

	
</div><!-- form-group -->

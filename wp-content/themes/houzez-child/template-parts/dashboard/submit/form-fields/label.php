<?php
	$title  = '';
	$parent = 0;
	$name   = 'prop_labels';
if ( isset( $args['title'] ) && ! empty( $args['title'] ) ) {
	$title = $args['title'];
}
if ( isset( $args['parent'] ) && ! empty( $args['parent'] ) ) {
	$parent = $args['parent'];
}
if ( isset( $args['name'] ) && ! empty( $args['name'] ) ) {
	$name = $args['name'];
}
?>
<div class="form-group">
	<label for="prop_label"><?php echo $title; ?></label>
		
	<?php
		$property_types_terms = get_terms(
			array(
				'property_label',
			),
			array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
				'parent'     => $parent,
			)
		);
		if ( houzez_edit_property() ) {
			global $property_data;
			$term_id = houzez_taxonomy_id_by_postID( $property_data->ID, 'property_label' );
			houzez_get_taxonomies_with_id_value_as_radio( 'property_label', $property_types_terms, $term_id, ' ', 'radio', $name );
		} else {
			houzez_get_taxonomies_with_id_value_as_radio( 'property_label', $property_types_terms, -1, ' ', 'radio', $name );
		}
		?>

	
</div><!-- form-group -->

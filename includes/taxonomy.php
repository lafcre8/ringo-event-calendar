<?php
/**
 * Taxonomy API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Register and setup the Event custom taxonomies.
 *
 * @since 0.1.0
 * @return void
 */
function lafrec_register_taxonomies() {
	$taxonomies = lafrec_get_taxonomies();

	foreach ( $taxonomies as $taxonomy ) {
		$labels = array(
			'name' => _x( 'Event Category', 'taxonomy general name', LAFREC_TEXT_DOMAIN ),
			'singular_name' => _x( 'Category', 'taxonomy singular name', LAFREC_TEXT_DOMAIN ),
			'search_items' => __( 'Search Categories', LAFREC_TEXT_DOMAIN ),
			'popular_items' => null,
			'all_items' => __( 'All Categories', LAFREC_TEXT_DOMAIN ),
			'parent_item' => __( 'Parent Category', LAFREC_TEXT_DOMAIN ),
			'parent_item_colon' => __( 'Parent Category:', LAFREC_TEXT_DOMAIN ),
			'edit_item' => __( 'Edit Category', LAFREC_TEXT_DOMAIN ),
			'view_item' => __( 'View Category', LAFREC_TEXT_DOMAIN ),
			'update_item' => __( 'Update Category', LAFREC_TEXT_DOMAIN ),
			'add_new_item' => __( 'Add New Category', LAFREC_TEXT_DOMAIN ),
			'new_item_name' => __( 'New Category Name', LAFREC_TEXT_DOMAIN ),
			'separate_items_with_commas' => null,
			'add_or_remove_items' => null,
			'choose_from_most_used' => null,
			'not_found' => __( 'No categories found.', LAFREC_TEXT_DOMAIN ),
			'no_terms' => __( 'No categories', LAFREC_TEXT_DOMAIN ),
			'items_list_navigation' => __( 'Categories list navigation', LAFREC_TEXT_DOMAIN ),
			'items_list' => __( 'Categories list', LAFREC_TEXT_DOMAIN )
		);
		$labels = apply_filters( 'lafrec_taxonomy_labels', $labels, $taxonomy );

		$caps = array();
		$args = array(
			'labels'                => $labels,
			'description'           => '',
			'public'			          => true,
			'hierarchical'          => true,
			'show_ui'               => null,
			'show_in_menu'          => null,
			'show_in_nav_menus'     => null,
			'show_tagcloud'         => null,
			'show_in_quick_edit'    => null,
			'show_admin_column'     => false,
			'meta_box_cb'           => null,
			'capabilities'          => $caps,
			'rewrite'               => true,
			'query_var'             => $taxonomy,
			'update_count_callback' => ''
		);

		$object_types = lafrec_get_post_types();
		$object_types = apply_filters( 'lafrec_taxonomy_object_types', $object_types, $taxonomy );

		$args = apply_filters( 'lafrec_taxonomy_args', $args, $taxonomy );

		register_taxonomy( $taxonomy, $object_types, $args );
	}

}

/**
 * Get Event taxonomies.
 *
 * @since 0.1.0
 * @return array Array for taxonomy slugs.
 */
function lafrec_get_taxonomies() {
	static $taxonomies;

	if ( is_null( $taxonomies ) ) {
		$taxonomies = apply_filters( 'lafrec_get_taxonomies', array( 'lafrec_category' ) );
	}

	return $taxonomies;
}

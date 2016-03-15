<?php
/**
 * Post type API.
 *
 * @package Lafrec
 */

/**
 * Register and setup the Event custom post type.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_register_post_types() {
	$post_types = lafrec_get_post_types();

	foreach ( $post_types as $post_type ) {
		$labels = array(
			'name'									=> _x( 'Events', 'post type general name', LAFREC_TEXT_DOMAIN ),
			'singular_name'					=> _x( 'Event', 'post type singular name', LAFREC_TEXT_DOMAIN ),
			'add_new'								=> _x( 'Add New', 'event', LAFREC_TEXT_DOMAIN ),
			'add_new_item'					=> __( 'Add New Event', LAFREC_TEXT_DOMAIN ),
			'edit_item'							=> __( 'Edit Event', LAFREC_TEXT_DOMAIN ),
			'new_item'							=> __( 'New Event', LAFREC_TEXT_DOMAIN ),
			'view_item'							=> __( 'View Event', LAFREC_TEXT_DOMAIN ),
			'search_items'					=> __( 'Search Events', LAFREC_TEXT_DOMAIN ),
			'not_found'							=> __( 'No Events found.', LAFREC_TEXT_DOMAIN ),
			'not_found_in_trash'		=> __( 'No Events found in Trash', LAFREC_TEXT_DOMAIN ),
			'parent_item_colon'			=> __( 'Parent Evnets:', LAFREC_TEXT_DOMAIN ),
			'all_items'							=> __( 'All Event', LAFREC_TEXT_DOMAIN ),
			'featured_image'				=> __( 'Featured Image', LAFREC_TEXT_DOMAIN ),
			'set_featured_image'		=> __( 'Set featured image', LAFREC_TEXT_DOMAIN ),
			'remove_featured_image'	=> __( 'Remove featured image', LAFREC_TEXT_DOMAIN ),
			'use_featured_image'		=> __( 'Use as featured image', LAFREC_TEXT_DOMAIN )
		);
		$labels = apply_filters( 'lafrec_post_type_labels', $labels, $post_type );

		$args = array(
			'label' => $post_type,
			'labels' => $labels,
			'description' => __( 'Event infomation', LAFREC_TEXT_DOMAIN ),
			'public' => true,
			//'exclude_from_search' => false,
			//'publicly_queryable' => true,
			//'show_ui' => true,
			//'show_in_nav_menus' => true,
			//'show_in_menu' => true,
			//'show_in_admin_bar' => true,
			'menu_position' => 25,
			'menu_icon' => null,
			//'capability_type' => 'post',
			//'capabilities' => array(),
			//'map_meta_cap' => null
			'hierarchical' => false,
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			//'register_meta_box_cb' => null,
			//'taxonomies' => null,
			'has_archive' => true,
			//'rewrite' => true,
			//'query_var' => true,
			//'can_export' => true
		);
		$args = apply_filters( 'lafrec_post_type_args', $args, $post_type );

		register_post_type( $post_type, $args );
	}

}

/**
 * Get event custom post types.
 *
 * @since 0.1.0
 *
 * @return array Array for event post type slugs.
 */
function lafrec_get_post_types() {
	static $post_types;

	if ( is_null( $post_types ) ) {
		$post_types = apply_filters( 'lafrec_post_types', array( 'lafrec_event' ) );
	}

	return $post_types;
}

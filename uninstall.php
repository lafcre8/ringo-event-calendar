<?php
/**
 * Uninstall Ringo Event Calendar.
 *
 * @package Lafrec
 *
 * @since 0.1.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require( 'ringo-event-calendar.php' );

function lafrec_uninstall() {

	if ( is_multisite() ) {
		$blogs = wp_get_sites();
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog[ 'blog_id' ] );
			lafrec_process_uninstall();
			restore_current_blog();
		}
	} else {
		lafrec_process_uninstall();
	}
}

function lafrec_process_uninstall() {
	global $wpdb, $wp_roles;

	/**
	 * Delete Post types
	 */
	$post_types = lafrec_get_post_types();
	$taxonomies = lafrec_get_taxonomies();

	foreach ( $post_types as $post_type ) {
		$taxonomies = array_merge( $taxonomies, get_object_taxonomies( $post_type ) );
		$event_args = array(
			'post_type' => $post_type,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$events = get_posts( $event_args );

		if ( count( $events ) > 0 ) {
			foreach ( $events as $event ) {
				wp_delete_post( $event, true);
			}
		}
	}

	/**
	 * Delete Taxonomies
	 */
	$taxonomies = array_filter( $taxonomies );
	$taxonomies = array_unique( $taxonomies );

	foreach ( $taxonomies as $taxonomy ) {
		$sql = "
			SELECT
				 t.*,
				 tt.*
			FROM
				{$wpdb->terms} AS t
			INNER JOIN
				{$wpdb->term_taxonomy} AS tt
			ON
				t.term_id = tt.term_id
			WHERE
				tt.taxonomy IN ( %s )
			ORDER BY t.name ASC";
		$pstmt = $wpdb->prepare( $sql, $taxonomy );
		$terms = $wpdb->get_results( $pstmt );

		if ( $terms ) {
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
				$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
			}
		}
		$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
	}

	/**
	 * Placeholder for Delete Sidebar widget
	 */

	/**
	 * Placeholder for Delete Roles and Caps
	 */

	/**
	 * Placeholder for Delete WP Cron jobs
	 */

	/**
	 * Drop tables
	 */
	$wpdb->query( 'DROP TABLE IF EXISTS ' . lafrec_get_schedules_table_name() );

	/**
	 * Delete Options
	 */
	delete_option( 'lafrec_options' );
}

lafrec_uninstall();

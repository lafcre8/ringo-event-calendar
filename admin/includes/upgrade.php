<?php
/**
 * Plugin Upgrade API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Install plugin.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_install( $network_wide = false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			lafrec_run_install();
			restore_current_blog();
		}
	} else {
		lafrec_run_install();
	}

}

function lafrec_run_install() {

	lafrec_load_textdomain();

	// Setup the Event custom post type
	lafrec_register_post_types();

	// Setup the Event taxonomies
	lafrec_register_taxonomies();

	// Flush rewrite rules so that users can access custom post types on the front-end right away
	flush_rewrite_rules( false );

	// Initialize plugin options
	lafrec_update_option( 'version', LAFREC_VERSION );

	// Setup roles and caps
	lafrec_setup_roles();

	// Setup database tables
	lafrec_create_schedules_table();
	lafrec_update_option( 'db_version', LAFREC_DB_VERSION );
}

/**
 * Upgrade.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_upgrade() {
		$current_version = lafrec_get_option( 'version' );
		if ( $current_version !== LAFREC_VERSION ) {
			lafrec_update_option( 'version', LAFREC_VERSION );
		}

		$current_db_version = (int) lafrec_get_option( 'db_version' );
		if ( $current_db_version === LAFREC_DB_VERSION ) {
			return;
		}
		lafrec_update_option( 'db_version', LAFREC_DB_VERSION );
}

function lafrec_on_wpmu_new_blog(  $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	if ( is_plugin_active_for_network( plugin_basename( LAFREC_PLUGIN_FILE ) ) ) {
		switch_to_blog( $blog_id );
		lafrec_install();
		restore_current_blog();
	}
}

function lafrec_on_wpmu_drop_tables( $tables, $blog_id ) {
	switch_to_blog( $blog_id );

	if ( lafrec_exists_schedules_table() ) {
		$tables[] = lafrec_get_schedules_table_name();
	}

	restore_current_blog();

	return $tables;
}

function lafrec_deactivate() {
	// Nothin to do
}

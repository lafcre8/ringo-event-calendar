<?php
/*
Plugin Name: Ringo Event Calendar
Version: 0.3.0
Description: Ringo is simple event calendar that originally designed for the wedding hall web sites.
Author: LafCreate
Author URI: http://www.lafcreate.com
Plugin URI: http://www.lafcreate.com
Text Domain: ringo-event-calendar
Domain Path: /languages
*/
/*
Copyright (C) 2016 LafCreate (email: info at lafcreate.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Prevent directly access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lafrec_meta = get_file_data(
	__FILE__,
 	array(
		'version' => 'Version',
		'text_domain' => 'Text Domain',
		'domain_path' => 'Domain Path'
	)
);

define( 'LAFREC_VERSION', $lafrec_meta['version'] );
define( 'LAFREC_DB_VERSION', 1 );

define( 'LAFREC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LAFREC_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'LAFREC_PLUGIN_FILE', __FILE__ );
define( 'LAFREC_TEXT_DOMAIN', $lafrec_meta['text_domain'] );
define( 'LAFREC_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . $lafrec_meta['domain_path'] );

define( 'LAFREC_CURRENT_TIME', current_time( 'timestamp' ) );

require( LAFREC_PLUGIN_DIR . 'includes/option.php' );

global $lafrec_options;
lafrec_load_options();

require( LAFREC_PLUGIN_DIR . 'includes/functions.php' );
require( LAFREC_PLUGIN_DIR . 'includes/l10n.php' );
require( LAFREC_PLUGIN_DIR . 'includes/capabilities.php' );
require( LAFREC_PLUGIN_DIR . 'includes/template.php' );
require( LAFREC_PLUGIN_DIR . 'includes/scripts.php' );
require( LAFREC_PLUGIN_DIR . 'includes/link-template.php' );
require( LAFREC_PLUGIN_DIR . 'includes/widgets.php' );
require( LAFREC_PLUGIN_DIR . 'includes/taxonomy.php' );
require( LAFREC_PLUGIN_DIR . 'includes/schedule.php' );
require( LAFREC_PLUGIN_DIR . 'includes/calendar.php' );
require( LAFREC_PLUGIN_DIR . 'includes/post.php' );
require( LAFREC_PLUGIN_DIR . 'includes/class-lafrec-calendar.php' );
require( LAFREC_PLUGIN_DIR . 'includes/class-lafrec-calendar-day.php' );
require( LAFREC_PLUGIN_DIR . 'includes/class-lafrec-schedule.php' );
require( LAFREC_PLUGIN_DIR . 'includes/widgets/class-lafrec-widget-schedule-list.php' );

require( LAFREC_PLUGIN_DIR . 'admin/includes/upgrade.php' );
require( LAFREC_PLUGIN_DIR . 'admin/includes/meta-boxes.php' );
require( LAFREC_PLUGIN_DIR . 'admin/includes/ajax-actions.php' );
require( LAFREC_PLUGIN_DIR . 'admin/includes/event.php' );
require( LAFREC_PLUGIN_DIR . 'admin/includes/template.php' );

register_activation_hook( LAFREC_PLUGIN_FILE, 'lafrec_install' );
register_deactivation_hook( LAFREC_PLUGIN_FILE, 'lafrec_deactivate' );

add_action( 'wpmu_new_blog', 'lafrec_on_wpmu_new_blog', 10, 6 );
add_filter( 'wpmu_drop_tables', 'lafrec_on_wpmu_drop_tables', 10, 2 );

add_action( 'plugins_loaded', 'lafrec_load_textdomain' );
add_action( 'init', 'lafrec_register_post_types' );
add_action( 'init', 'lafrec_register_taxonomies' );
add_action( 'init', 'lafrec_register_save_post_hooks' );
add_action( 'widgets_init', 'lafrec_register_widgets' );
add_action( 'wp_enqueue_scripts', 'lafrec_load_scripts' );
add_action( 'delete_post', 'lafrec_delete_schedule_on_delete_post' );

add_action( 'admin_init', 'lafrec_upgrade' );
add_action( 'admin_menu', 'lafrec_add_meta_box' );
add_action( 'admin_enqueue_scripts', 'lafrec_load_admin_scripts' );
add_action( 'wp_ajax_lafrec_get_admin_calendar', 'lafrec_ajax_get_admin_calendar' );

add_shortcode( 'lafrec_calendar', 'lafrec_calendar_shortcode' );

unset( $lafrec_meta );

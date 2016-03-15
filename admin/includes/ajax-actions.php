<?php
/**
 * Ajax handlers.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Ajax handler for calendar for admin.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_ajax_get_admin_calendar() {
	check_ajax_referer( 'lafrec-get-admin-calendar', 'nonce' );

	$post_id = null;
	if ( isset( $_POST['post_id'] ) ) {
		if ( preg_match( '/^[0-9]+$/', $_POST['post_id'] ) ) {
			$post_id = (int) $_POST['post_id'];
		}
	}

	$year = '';
	if ( isset( $_POST['year'] ) ) {
		$year = $_POST['year'];
		$year = sprintf( '%04d', $year );
	}

	$month = '';
	if ( isset( $_POST['month'] ) ) {
		$month = $_POST['month'];
		$month = sprintf( '%02d', $month );
	}

	if ( ! checkdate( (int) $month, 1, (int) $year ) ) {
		wp_send_json_error();
	}

	$start = new DateTime( "{$year}-{$month}-01" );
	$end = new DateTime( $start->format( 'Y-m-t' ) );

	$schedules = array();
	if ( ! is_null( $post_id ) ) {
		$schedule_args = array(
			'from' => $start,
			'to' => $end,
			'event_post_id' => $post_id,
			'event_post_status' => 'any',
			'limit' => -1
		);
		$schedules = lafrec_get_schedules( $schedule_args );
	}

	$calendar_args = array(
		'start' => $start,
		'end' => $end,
		'schedules' => $schedules
	);
	$calendar = new Lafrec_Calendar( $calendar_args );

	$data = array(
		'calendar_html' => lafrec_admin_calendar( $calendar )
	);
	wp_send_json_success( $data );
}

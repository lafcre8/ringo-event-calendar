<?php
/**
 * Admin Meta Boxes.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Add plugin meta boxes.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_add_meta_box() {
	// Event schedule meta box
	$post_types = lafrec_get_post_types();
	foreach ( $post_types as $post_type ) {
		add_meta_box(
			'ringo',
			__( 'Event schedule', LAFREC_TEXT_DOMAIN ),
			'lafrec_event_schedule_meta_box',
			$post_type,
			'normal',
			'high',
			null
		);
	}
}

/**
 * Displays event schedule form fields.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_event_schedule_meta_box() {
	$start = new DateTime( date_i18n( 'Y-m-01', LAFREC_CURRENT_TIME ) );
	$end = new DateTime( $start->format( 'Y-m-t' ) );

	$event_times = get_post_meta( get_the_ID(), 'lafrec_event_times', true );
	$post_id = (int) get_the_ID();

	$schedules = array();
	if ( $post_id > 0 ) {
		// Retrieve schedule data
		$schedule_args = array(
			'from' => $start,
			'to' => $end,
			'event_post_id' => $post_id,
			'event_post_status' => 'any',
			'limit' => -1
		);
		$schedules = lafrec_get_schedules( $schedule_args );
	}

	// Create calendar object
	$calendar_args = array(
		'start' => $start,
		'end' => $end,
		'schedules' => $schedules
	);
	$calendar = new Lafrec_Calendar( $calendar_args );

	// Render form
	$nonce = wp_create_nonce( 'lafrec-edit-event-' . $post_id );
	$ajax_nonce = wp_create_nonce( 'lafrec-get-admin-calendar' );

	$html = '';
	$html .= '<p><strong>' . __( 'Date', LAFREC_TEXT_DOMAIN ) . '</strong></p>';

	$html .= '<div class="lafrec-admin-calendars">';
	$html .= '<div class="lafrec-calendar-loader"><img src="' . LAFREC_PLUGIN_DIR_URL . 'images/spinner-2x.gif"></div>';
	$html .= lafrec_admin_calendar( $calendar );
	$html .= '</div>';

	$html .= '<hr>';

	$html .= '<p><strong>' . __( 'Time', LAFREC_TEXT_DOMAIN ) . '</strong></p>';

	$html .= '<table class="lafrec-event-time-table">';
	$html .= '<tr class="lafrec-event-time-table-head">';
	$html .= '<th>';
	$html .= '</th>';
	$html .= '<th>';
	$html .=  __( 'Start', LAFREC_TEXT_DOMAIN );
	$html .= '</th>';
	$html .= '<th>';
	$html .=  __( 'End', LAFREC_TEXT_DOMAIN );
	$html .= '</th>';
	$html .= '<th>';
	$html .= '</th>';
	$html .= '</tr>';

	if ( empty( $event_times ) ) {

		$html .= '<tr class="lafrec-event-time-row" data-number="1">';
		$html .= '<th>';
		$html .= '<span class="lafrec-event-time-number">1</span>';
		$html .= '</th>';
		$html .= '<td>';
		$html .= '<input type="text" class="lafrec-event-start-time" name="lafrec_event_start_time[]" value="">';
		$html .= '</td>';
		$html .= '<td>';
		$html .= '<input type="text" class="lafrec-event-end-time" name="lafrec_event_end_time[]" value="">';
		$html .= '</td>';
		$html .= '<td class="lafrec-remove-event-time">';
		$html .= '<a href="#" class="button lafrec-remove-event-time-button" style="visibility: hidden;">' . __( 'Remove', LAFREC_TEXT_DOMAIN ) . '</a>';
		$html .= '</td>';
		$html .= '</tr>';


	} else {

		foreach ( $event_times as $index => $event_time ) {
			$number = $index + 1;
			$html .= '<tr class="lafrec-event-time-row" data-number="' . $number . '">';
			$html .= '<th>';
			$html .= '<span class="lafrec-event-time-number">' . $number . '</span>';
			$html .= '</th>';
			$html .= '<td>';
			$html .= '<input type="text" class="lafrec-event-start-time" name="lafrec_event_start_time[]" value="' . esc_attr( $event_time[ 'start' ] ) . '">';
			$html .= '</td>';
			$html .= '<td>';
			$html .= '<input type="text" class="lafrec-event-end-time" name="lafrec_event_end_time[]" value="' . esc_attr( $event_time[ 'end' ] ) . '">';
			$html .= '</td>';
			$html .= '<td class="lafrec-remove-event-time">';
			$html .= '<a href="#" class="button lafrec-remove-event-time-button"' . ( $number === 1 ? ' style="visibility: hidden;' : '' ) . '">' . __( 'Remove', LAFREC_TEXT_DOMAIN ) . '</a>';
			$html .= '</td>';
			$html .= '</tr>';

		}

	}

	$html .= '</table>';
	$html .= '<div class="lafrec-add-event-time">';
	$html .= '<a href="#" class="button button-primary button-large lafrec-add-event-time-button">' . __( 'Add Time', LAFREC_TEXT_DOMAIN ) . '</a>';
	$html .= '</div>';

	$html .= '<input type="hidden" name="lafrec_nonce" value="' . $nonce . '" />';
	$html .= '<input type="hidden" name="lafrec_ajax_nonce" value="' . $ajax_nonce . '" />';
	$html .= '<input type="hidden" name="lafrec_event_post_id" value="' . $post_id . '" />';
	echo $html;
}

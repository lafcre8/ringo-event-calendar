<?php
/**
 * Event Post API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Register save post hooks.
 *
 * @return void
 */
function lafrec_register_save_post_hooks() {
	$post_types = lafrec_get_post_types();

	foreach ( $post_types as $post_type ) {
		add_action( 'save_post_' . $post_type, 'lafrec_save_event_post', 10, 3 );
	}
}

/**
 * Save event schedule data on save post.
 *
 * @param int $post_id Post ID.
 * @param WP_Post $post Post object.
 * @param bool $update True if update, false if insert.
 *
 * @return void
 */
function lafrec_save_event_post( $post_id, $post, $update ) {
	// Prevent action if new post or trash
	if ( empty( $_POST ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Verify nonce
	$nonce = '';
	if ( isset( $_POST['lafrec_nonce'] ) ) {
		$nonce = $_POST['lafrec_nonce'];
	}

	$action = 'lafrec-edit-event-' . get_the_ID();
	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		wp_die( __( 'Invalid operation', LAFREC_TEXT_DOMAIN ) );
	}

	// Get schedule data
	$schedules = array();
	if ( isset( $_POST['lafrec_changed_dates'] ) ) {
		$schedules = (array) json_decode( stripslashes( $_POST['lafrec_changed_dates'] ), true );
	}

	// Get event time
	$event_times = array();

	$start_times = array();
	if ( isset( $_POST['lafrec_event_start_time'] ) ) {
		$start_times = (array) $_POST['lafrec_event_start_time'];
	}

	$end_times = array();
	if ( isset( $_POST['lafrec_event_end_time'] ) ) {
		$end_times = (array) $_POST['lafrec_event_end_time'];
	}

	foreach ( range( 0, 4 ) as $index ) {

		$event_time_data = array();

		$event_time_data[ 'event_time_number' ] = $index + 1;
		$event_time_data[ 'start_time' ] = '00:00';
		$event_time_data[ 'end_time' ] = '00:00';
		$event_time_data[ 'status' ] = 'closed';

		if ( isset( $start_times[ $index ] ) && isset( $end_times[ $index ] ) ) {
			$event_time_data[ 'start_time' ] = $start_times[ $index ];
			$event_time_data[ 'end_time' ] = $end_times[ $index ];
			$event_time_data[ 'status' ] = 'open';
		}

		$event_times[] = $event_time_data;
	}

	// Insert schedules if checkbox is checked
	// Mark for delete if checkbox is unchecked
	$dates_delete = array();
	foreach ( $schedules as $date => $checked ) {

		if ( $checked ) {

			// Dates to add
			foreach ( $event_times as $event_time_data ) {

				$data = array(
					'event_post_id' => $post_id,
					'event_time_number' => $event_time_data[ 'event_time_number' ],
					'status' => $event_time_data[ 'status' ],
					'start_date' => $date,
					'start_time' => $event_time_data[ 'start_time' ],
					'end_date' => $date,
					'end_time' => $event_time_data[ 'end_time' ],
				);
				lafrec_insert_schedule( $data );

			}

		} else {

			// Dates to delete
			$dates_delete[] = new DateTime( $date );

		}
	}

	// Delete schedules
	if ( count( $dates_delete ) > 0 ) {
		$where = array(
			'event_post_id' => $post_id
		);
		lafrec_delete_by_date( $dates_delete, $where );
	}

	// Update event time
	$post_meta_event_times = array();
	foreach ( $event_times as $event_time_data ) {

		// Update database
		$data = array(
			'status' => $event_time_data[ 'status' ],
			'start_time' => $event_time_data[ 'start_time' ],
			'end_time' => $event_time_data[ 'end_time' ]
		);
		$where = array(
			'event_post_id' => $post_id,
			'event_time_number' => $event_time_data[ 'event_time_number' ],
		);
		lafrec_update_schedule( $data, $where );

		// Prepare update post meta
		if ( $event_time_data[ 'status' ] === 'open' ) {
			$post_meta_event_times[] = array(
				'start' => $event_time_data[ 'start_time' ],
				'end' => $event_time_data[ 'end_time' ]
			);
		}

	}
	update_post_meta( $post_id, 'lafrec_event_times', $post_meta_event_times );

}

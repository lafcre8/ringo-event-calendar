<?php
/**
 * Calendar API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 *
 *
 * @since 0.1.0
 * @return void
 */
function lafrec_calendar_shortcode( $atts, $content = null ) {
	$defaults = array(
		'from' => '',
		'to' => '',
		'week' => '',
		'month' => '',
		'day' => '',
		'schedule_order' => 'ASC',
		'template' => 'calendar.php',
		'single_schedule' => false,
		'event_post_type' => lafrec_get_post_types()
	);
	$a = shortcode_atts( $defaults, $atts, 'lafrec_calendar' );

	// Setup 'from' datetime
	$from_string = $a[ 'from' ];
	$from = lafrec_parse_date( $from_string, 'Y-m-01' );

	// Setup 'to' datetime
	$to_string = $a['to'];
	if ( $to_string !== '' ) {
		$to = lafrec_parse_date( $to_string );
	} else {
		$to = new DateTime( $from->format( 'Y-m-d' ) );

		$format = '';
		$week = $a[ 'week' ];
		$month = $a[ 'month' ];
		$day = $a[ 'day' ];

		if ( $week !== '' ) {
			$format .= '+' . (int) $week . ' weeks ';
		}

		if ( $month !== '' ) {
			$format .= '+' . (int) $month . ' month ';
		}

		if ( $day !== '' ) {
			$format .= '+' . (int) $day . ' day ';
		}
		$format = trim( $format );

		if ( $format === '' ) {
			$format = 'last day of ' . $from->format( 'Y-m-d' );
		}

		$to->modify( $format );
	}

	// Setup post type
	$event_post_type = $a['event_post_type'];
	if ( is_string( $event_post_type ) ) {
		$event_post_type = implode( ',', trim( $event_post_type ) );
		$event_post_type = array_map( 'trim', $event_post_type );
		$event_post_type = array_filter( $event_post_type );
	}

	// Single schedule per event
	$single_schedule = $a[ 'single_schedule' ];
	if ( $single_schedule === 'false' ) {
		$single_schedule = false;
	}
	$single_schedule = (bool) $single_schedule;

	// Get schedules
	$schedule_args = array(
		'from' => lafrec_get_first_date_of_week( $from ),
		'to' => lafrec_get_last_date_of_week( $to ),
		'event_post_type' => $event_post_type,
		'order' => $a[ 'schedule_order' ],
		'single_schedule' => $single_schedule,
		'limit' => -1
	);
	$schedules = lafrec_get_schedules( $schedule_args );

	// Create calendar
	$calendar_args = array(
		'start' => $from,
		'end' => $to,
		'first_day_of_week' => apply_filters( 'lafrec_first_day_of_week', 0 ),
		'schedules' => $schedules
	);
	$calendar = new Lafrec_Calendar( $calendar_args );

	return lafrec_calendar( $calendar, $a['template'] );
}

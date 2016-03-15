<?php
/**
 * Lafrec functions.
 *
 * @package Lafrec
 * @since 0.1.0
 */

function lafrec_parse_date( $value = null, $default_format = 'Y-m-d' ) {
	if ( $value instanceof DateTime ) {
		$datetime = new DateTime( $value->format( 'Y-m-d H:i:s' ) );
	} else if ( is_string( $value ) && $value !== '' ) {
		$datetime = new DateTime( date_i18n( 'Y-m-d H:i:s', strtotime( $value ) ) );
	} else {
		$datetime = new DateTime( date_i18n( $default_format, LAFREC_CURRENT_TIME ) );
	}
	return $datetime;
}

function lafrec_get_first_date_of_week( $date = null, $first_day_of_week = 0 ) {
	$datetime = lafrec_parse_date( $date );
	$day = $datetime->format( 'w' );

	$first_day = (int) $first_day_of_week;
	$offset = $day - $first_day;
	if ( $offset < 0 ) {
		$offset = 7 + $offset;
	}
	$datetime->modify( "-{$offset} day" );

	return $datetime;
}

function lafrec_get_last_date_of_week( $date = null, $first_day_of_week = 0 ) {
	$datetime = lafrec_parse_date( $date );
	$day = $datetime->format( 'w' );

	$last_day = $first_day_of_week - 1;
	if ( $last_day < 0 ) {
		$last_day = 6;
	}
	$offset = $last_day - $day;
	if ( $offset < 0 ) {
		$offset = 7 + $offset;
	}
	$datetime->modify( "+{$offset} day" );

	return $datetime;
}

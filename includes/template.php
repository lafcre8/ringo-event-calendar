<?php
/**
 * Template functions.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Render calendar template.
 *
 * @since 0.1.0
 *
 * @param Lafrec_Calendar $calendar Calendar object
 * @return string Calendar HTML
 */
function lafrec_calendar( $calendar, $template_name = 'calendar.php' ) {
	$_content = '';

	$template = lafrec_locate_template( $template_name );
	if ( $template !== false ) {
		ob_start();
		ob_implicit_flush( 0 );
		include $template;
		$_content = ob_get_clean();
	}

	return $_content;
}

/**
 * Retrieve the file path of the highest priority template file that exists.
 *
 * @since 0.1.0
 *
 * @param string $template Template file to search for.
 * @return string|bool The template filename if one is located. False if template is not found.
 */
function lafrec_locate_template( $template_name ) {
	$dirs = array(
		get_stylesheet_directory() . '/lafrec-templates',
		get_template_directory() . '/lafrec-templates',
		LAFREC_PLUGIN_DIR . 'lafrec-templates'
	);

	$located = false;

	foreach ( $dirs as $dir ) {
		$path = $dir . DIRECTORY_SEPARATOR . basename( $template_name );
		if ( file_exists( $path ) ) {
			$located = $path;
			break;
		}
	}
	return $located;
}

/**
 * Retrieve the classes for the calendar day element.
 *
 * @since 0.1.0
 *
 * @param Lafrec_Calendar_Day $calendar_day Calendar day object.
 * @return string Class string.
 */
function lafrec_get_calendar_day_class( Lafrec_Calendar_Day $calendar_day ) {
	$classes = array();
	$classes[] = 'lafrec-calendar-day';
	$classes[] = 'lafrec-calendar-day-w-' . $calendar_day->format( 'w' );
	$classes[] = 'lafrec-calendar-day-' . $calendar_day->format( 'Ymd' );

	$today = new DateTime( date_i18n( 'Y-m-d', LAFREC_CURRENT_TIME ) );

	if ( $calendar_day->format( 'Ymd' ) ===  $today->format( 'Ymd' ) ) {
		$classes[] = 'lafrec-calendar-day-today';
	}

	if ( $calendar_day->has_schedule() ) {
		$classes[] = 'lafrec-calendar-day-has-schedule';
	}

	$classes = apply_filters( 'lafrec_calendar_day_class', $classes, $calendar_day );
	$class_string = implode( ' ', $classes );

	return $class_string;
}

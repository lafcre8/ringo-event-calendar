<?php
/**
 * Template Administration API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Return a calendar HTML for meta box.
 *
 * @since 0.1.0
 *
 * @param Lafrec_Calendar $calendar Calendar object to display.
 * @return string Calendar HTML.
 *
 */
function lafrec_admin_calendar( Lafrec_Calendar $calendar ) {
	$html = '';

	$id = 'lafrec-admin-calendar-' . $calendar->start->format( 'Ym' );

	$html .= '<div id="' . $id . '" class="lafrec-admin-calendar">';
	$html .= '<div class="lafrec-admin-calendar-caption">';
	$html .= $calendar->start->format( _x( 'F Y', 'admin calendar year month format', LAFREC_TEXT_DOMAIN ) );
	$html .= '</div>';

	$prev_month_end = new DateTime( $calendar->start->format( 'Y-m-01' ) );
	$prev_month_end->modify( '-1 day' );

	$next_month_start = new DateTime( $calendar->end->format( 'Y-m-t' ) );
	$next_month_start->modify( '+1 day' );

	$html .= '<div class="lafrec-calendar-pager">';
	$html .= '<a class="lafrec-calendar-pager-link button" href="#" data-target-year="' . $prev_month_end->format( 'Y' ) . '" data-target-month="' . $prev_month_end->format( 'm' ) . '">' . _x( '&lt; Prev', 'admin calendar pager', LAFREC_TEXT_DOMAIN ) . '</a>';
	$html .= '&nbsp;';
	$html .= '<a class="lafrec-calendar-pager-link button" href="#" data-target-year="' . $next_month_start->format( 'Y' ) . '" data-target-month="' . $next_month_start->format( 'm' ) . '">' . _x( 'Next &gt;', 'admin calendar pager', LAFREC_TEXT_DOMAIN ) . '</a>';
	$html .= '</div>';
	$html .= '<table class="lafrec-admin-calendar-table">';

	$day_names = $calendar->get_day_names();

	$html .= '<tr class="lafrec-admin-calendar-header">';
	foreach ( $day_names as $key => $day ) {
		$html .= '<th class="lafrec-admin-calendar-header-day">';
		$html .= '<a href="#" class="lafrec-admin-calendar-header-day-check" data-day-of-week="' . $key . '">' . $day  . '</a>';
		$html .= '</th>';
	}
	$html .= '</tr>';

	$start_ymd = (int) $calendar->start->format( 'Ymd' );
	$end_ymd = (int) $calendar->end->format( 'Ymd' );

	foreach ( $calendar->get_days() as $calendar_day ) {
		$day_of_week = (int) $calendar_day->format( 'w' );

		if ( $day_of_week === 0 ) {
			$html .= '<tr><td class="lafrec-admin-calendar-day">';
		} else {
			$html .= '</td><td class="lafrec-admin-calendar-day">';
		}

		if ( (int) $calendar_day->format( 'Ymd' ) >= $start_ymd && (int) $calendar_day->format( 'Ymd' ) <= $end_ymd ) {
			$id = 'lafrec-admin-calendar-day-' . $calendar_day->format( 'Ymd' );
			$label = $calendar_day->format( 'd' );

			$html .= '<label for="' . $id . '">' . $label . '</label>';
			$html .= '<div>';
			$html .= '<input';
			$html .= ' type="checkbox" id="' . $id . '" class="lafrec-admin-calendar-day-check"';
			$html .= ' data-day-of-week="' . $day_of_week . '"';
			$html .= ' name="" value="' . $calendar_day->format( 'Ymd' ) . '"';
			$html .= checked( $calendar_day->has_schedule(), true, false ) . '>';
			$html .= '</div>';
		}

		if ( $day === 6 ) {
			$html .= '</td></tr>';
		}
	}
	$html .= '</table>';
	$html .= '</div>';
	$html .= '<input type="hidden" name="lafrec_changed_dates" value="">';

	$html = apply_filters( 'lafrec_admin_calendar_html', $html, $calendar );

	return $html;
}

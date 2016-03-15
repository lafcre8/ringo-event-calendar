<?php
/**
 * Calendar class.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Calendar class
 *
 * @since 0.1.0
 */
class Lafrec_Calendar {
	public $start;
	public $end;
	public $period_start;
	public $period_end;
	public $first_day_of_week;
	public $last_day_of_week;
	private $days;
	private $day_names;

	public function __construct( $args = array() ) {
		$defaults = array(
			'start' => '',
			'end' => '',
			'schedules' => array(),
			'first_day_of_week' => 0,
			'day_names' => array(
				__( 'Sun', LAFREC_TEXT_DOMAIN ), // 0
				__( 'Mon', LAFREC_TEXT_DOMAIN ), // 1
				__( 'Tue', LAFREC_TEXT_DOMAIN ), // 2
				__( 'Wed', LAFREC_TEXT_DOMAIN ), // 3
				__( 'Thu', LAFREC_TEXT_DOMAIN ), // 4
				__( 'Fri', LAFREC_TEXT_DOMAIN ), // 5
				__( 'Sat', LAFREC_TEXT_DOMAIN )  // 6
			)
		);
		$args = wp_parse_args( $args, $defaults );
		$this->init( $args );
	}

	private function init( $args ) {
		$this->init_day_of_week( $args );
		$this->init_range( $args );
		$this->init_days( $args );
	}

	private function init_day_of_week( $args ) {
		// Setup first day of week
		$day = (int) $args['first_day_of_week'];

		if ( $day > 6 || $day < 0 ) {
			$day = 0;
		}
		$this->first_day_of_week = $day;

		// Setup day names
		$day_names = $args['day_names'];

		if ( $day > 0 ) {
			// We must preserve keys.
			// Key is expected to be day of week integer.
			$a = array_slice( $day_names, 0, $day, true );
			$b = array_slice( $day_names, $day, true );
			$day_names = $b + $a;
		}
		$this->day_names = $day_names;

		// Setup last day of week
		$last_day = $day - 1;
		if ( $last_day < 0 ) {
			$last_day = 6;
		}
		$this->last_day_of_week = $last_day;
	}

	private function init_range( $args ) {
		$start = $args['start'];
		$this->start = lafrec_parse_date( $start, 'Y-m-01' );

		$end = $args['end'];
		$end_datetime = lafrec_parse_date( $end );
		if ( $end_datetime->getTimeStamp() < $this->start->getTimeStamp() ) {
			$end_datetime->setTimestamp( strtotime( $this->start->format( 'Y-m-t' ) ) );
		}
		$this->end = $end_datetime;
	}

	private function init_days( $args ) {
		$this->days = array();

		// Setup period
		$period_start = lafrec_get_first_date_of_week( $this->start, $this->first_day_of_week );
		$period_end = lafrec_get_last_date_of_week( $this->end, $this->first_day_of_week );
		$period_end->modify( '+1 days' );
		$days = new DatePeriod( $period_start, new DateInterval( 'P1D' ), $period_end );

		$this->period_end = end( $days );
		$this->period_start = reset( $days );

		// Prepare schedule data
		$date_schedules = array();
		foreach ( (array) $args['schedules'] as $schedule ) {
			if ( ! $schedule instanceof Lafrec_Schedule ) {
				continue;
			}

			$schedule_key = $schedule->start->format( 'Ymd' );
			if ( ! array_key_exists( $schedule_key, $date_schedules ) ) {
				$date_schedules[$schedule_key] = array();
			}
			$date_schedules[$schedule_key][] = $schedule;
		}

		// Create calendar day
		foreach ( $days as $datetime ) {
			$schedules = array();

			$schedule_key = $datetime->format( 'Ymd' );
			if ( array_key_exists( $schedule_key, $date_schedules ) ) {
				$schedules = $date_schedules[$schedule_key];
			}

			$day_key = $schedule_key;
			$this->days[$day_key] = new Lafrec_Calendar_Day( $datetime, $schedules );
	 	}

	}

	public function get_days() {
		return $this->days;
	}

	public function get_weeks() {
		return array_chunk( $this->days, 7 );
	}

	public function get_day_names() {
		return $this->day_names;
	}

}

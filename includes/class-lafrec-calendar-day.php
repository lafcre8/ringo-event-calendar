<?php
/**
 * Calendar Day class.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Calendar Day class
 *
 * @since 0.1.0
 */
class Lafrec_Calendar_Day {
	private $schedules;
	private $event_posts;
	private $datetime;

	const SUNDAY = 0;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;

	public function __construct( DateTime $datetime, array $schedules = array() ) {
		$this->datetime = $datetime;
		$this->schedules = $schedules;
	}

	public function equals( $o ) {

		if ( $o instanceof Lafrec_Calendar_Day || $o instanceof DateTime ) {
			$datetime = $o;
		} else {
			$string = (string) $o;
			$datetime = new DateTime( $string );
		}

		$format = 'Ymd';
		return $this->format( $format ) === $datetime->format( $format );
	}

	public function equals_day_of_week( $day ) {
		return (int) $this->format( 'w' ) === (int) $day;
	}

	public function format( $string ) {
		return $this->datetime->format( $string );
	}

	public function is_first_day_of_month() {
		return $this->format( 'd' ) === '01';
	}

	public function is_last_day_of_month() {
		return $this->format( 'd' ) === $this->format( 't' );
	}

	public function has_schedule() {
		return ( count( $this->schedules ) > 0 );
	}

	public function get_schedules() {
		return $this->schedules;
	}

	public function count_schedules() {
		return count( $this->schedules );
	}

}

<?php
/**
 * Schedule class.
 *
 * @package Lafrec
 *
 * @since 0.1.0
 */

/**
 * Schedule object
 *
 * @since 0.1.0
 */
class Lafrec_Schedule {
	public $id;
	public $start;
	public $end;
	public $event_post;
	public $created_at;
	public $updated_at;

	public function __construct( $start, $end ) {
		$this->start = lafrec_parse_date( $start );
		$this->end = lafrec_parse_date( $end );
	}

	function __set( $name, $value ) {
		if ( $name === 'id' ) {
			$this->{$name} = (int) $value;
		} else if ( in_array( $name, array( 'start', 'end', 'created_at', 'updated_at' ) ) ) {
			$this->{$name} = lafrec_parse_date( $value );
		} else {
			$this->{$name} = $value;
		}
	}
}

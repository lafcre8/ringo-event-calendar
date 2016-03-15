<?php
class Tests_Functions extends WP_UnitTestCase {

	function test_lafrec_parse_date_string() {
		$string = '2016-01-15 10:23:12';
		$datetime = lafrec_parse_date( $string );
		$this->assertInstanceOf( 'DateTime', $datetime );
		$this->assertEquals( $string, $datetime->format( 'Y-m-d H:i:s' ) );
	}

	function test_lafrec_parse_date_datetime() {
		$datetime_1 = new DateTime( '2016-01-15 10:23:12' );
		$datetime_2 = lafrec_parse_date( $datetime_1 );
		$this->assertInstanceOf( 'DateTime', $datetime_1 );
		$this->assertEquals( $datetime_1, $datetime_2 );
		$this->assertNotSame( $datetime_1, $datetime_2 );
	}

	function test_lafrec_parse_date_empty_value() {
		$datetime_no_arg = lafrec_parse_date();
		$expected_no_arg = new DateTime( date_i18n( 'Y-m-d', LAFREC_CURRENT_TIME ) );
		$this->assertInstanceOf( 'DateTime', $datetime_no_arg );
		$this->assertEquals( $expected_no_arg, $datetime_no_arg );

		$datetime_default = lafrec_parse_date( null, 'Y-m-d H:i:s' );
		$expected_default = new DateTime( date_i18n( 'Y-m-d H:i:s', LAFREC_CURRENT_TIME ) );
		$this->assertInstanceOf( 'DateTime', $datetime_default );
		$this->assertEquals( $expected_default, $datetime_default );
	}

	function test_lafrec_get_first_date_of_week() {
		$date_1 = '2016-01-16'; // Saturday
		$datetime_1 = lafrec_get_first_date_of_week( $date_1 );
		$this->assertEquals( '2016-01-10', $datetime_1->format( 'Y-m-d' ) );
		$this->assertEquals( 0, $datetime_1->format( 'w' ) );

		$date_2 = '2016-01-10'; // Sunday
		$datetime_2 = lafrec_get_first_date_of_week( $date_2 );
		$this->assertEquals( '2016-01-10', $datetime_2->format( 'Y-m-d' ) );
		$this->assertEquals( 0, $datetime_2->format( 'w' ) );

		$date_3 = '2016-01-13'; // Wednesday
		$datetime_3 = lafrec_get_first_date_of_week( $date_3 );
		$this->assertEquals( '2016-01-10', $datetime_3->format( 'Y-m-d' ) );
		$this->assertEquals( 0, $datetime_3->format( 'w' ) );
	}

	function test_lafrec_get_last_date_of_week() {
		$date_1 = '2016-01-16'; // Saturday
		$datetime_1 = lafrec_get_last_date_of_week( $date_1 );
		$this->assertEquals( '2016-01-16', $datetime_1->format( 'Y-m-d' ) );
		$this->assertEquals( 6, $datetime_1->format( 'w' ) );

		$date_2 = '2016-01-10'; // Sunday
		$datetime_2 = lafrec_get_last_date_of_week( $date_2 );
		$this->assertEquals( '2016-01-16', $datetime_2->format( 'Y-m-d' ) );
		$this->assertEquals( 6, $datetime_2->format( 'w' ) );

		$date_3 = '2016-01-13'; // Wednesday
		$datetime_3 = lafrec_get_last_date_of_week( $date_3 );
		$this->assertEquals( '2016-01-16', $datetime_3->format( 'Y-m-d' ) );
		$this->assertEquals( 6, $datetime_3->format( 'w' ) );
	}
}

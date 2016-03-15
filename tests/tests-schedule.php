<?php
class Tests_Schedule extends WP_UnitTestCase {

	function test_lafrec_get_schedules_column_formats() {
		$column_formats = lafrec_get_schedules_column_formats();
		$expected = array(
			'id' => '%d',
			'event_post_id' => '%d',
			'event_time_number' => '%d',
			'status' => '%s',
			'start_date' => '%s',
			'start_time' => '%s',
			'end_date' => '%s',
			'end_time' => '%s',
			'created_at' => '%s',
			'updated_at' => '%s'
		);
		$this->assertCount( 10, $column_formats );
		$this->assertEquals( $expected, $column_formats );
	}

	function test_lafrec_get_schedules_table_name() {
		global $wpdb;

		$table_name = lafrec_get_schedules_table_name();
		$expected = $wpdb->prefix . 'lafrec_schedules';
		$this->assertEquals( $expected, $table_name );
	}

	function test_lafrec_get_schedule() {
		$r = lafrec_get_schedule( 1 );
	}

}

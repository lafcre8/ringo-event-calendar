<?php
class Tests_L10n extends WP_UnitTestCase {

	function test_load_textdomain() {
		$this->assertTrue( is_textdomain_loaded( LAFREC_TEXT_DOMAIN ) );
	}
}

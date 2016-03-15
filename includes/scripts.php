<?php
/**
 * Scripts functions.
 *
 * @package Lafrec
 */

/**
 * Load scripts
 *
 * @since 0.1.0
 * @return void
 */
function lafrec_load_scripts() {

	if ( apply_filters( 'lafrec_use_default_css', true ) ) {

		wp_enqueue_style(
			'lafrec',
			LAFREC_PLUGIN_DIR_URL . 'css/style.css'
		);

		if ( apply_filters( 'lafrec_is_scrollable', true ) ) {

			$min_width = '680px';

			global $content_width;
			if ( isset( $content_width ) ) {
				$min_width = $content_width . 'px';
			}
			$min_width = apply_filters( 'lafrec_min_width', $min_width );

			$css = <<<EOD
.lafrec-calendar {
	width: 0.1.0%;
	overflow-x: auto;
}

.lafrec-calendar .lafrec-calendar-table {
	font-size: inherit;
	min-width: {$min_width};
	width: auto;
}
EOD;
			$css = apply_filters( 'lafrec_scrollable_css', $css );

			wp_add_inline_style(
				'lafrec',
				$css
			);

		}

	}
}

/**
 * Load admin scripts
 *
 * @since 0.1.0
 * @return void
 */
function lafrec_load_admin_scripts( $hook_suffix ) {
	if ( $hook_suffix === 'post-new.php' || $hook_suffix === 'post.php' ) {
		wp_enqueue_script( 'json2' );
		wp_enqueue_script(
			'jquery-timepicker',
			LAFREC_PLUGIN_DIR_URL . 'js/jquery.timepicker.min.js'
		);
		wp_enqueue_style(
			'jquery-timepicker',
			LAFREC_PLUGIN_DIR_URL . 'css/jquery.timepicker.css'
		);
		wp_enqueue_script(
			'lafrec-admin-post',
			LAFREC_PLUGIN_DIR_URL . 'js/admin-post.js'
		);

		$timepicker_lang = array(
			'am' => _x( 'am', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'pm' => _x( 'pm', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'AM' => _x( 'AM', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'PM' => _x( 'PM', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'decimal' => _x( '.', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'mins' => _x( 'mins', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'hr' => _x( 'hr', 'timepicker', LAFREC_TEXT_DOMAIN ),
			'hrs' =>  _x( 'hrs', 'timepicker', LAFREC_TEXT_DOMAIN )
		);
		$timepicker_lang = apply_filters( 'lafrec_timepicker_lang', $timepicker_lang );

		$timepicker_start_args = array(
			'lang' => $timepicker_lang,
			'minTime' => '00:00',
			'maxTime' => '23:30',
			'step' => 10,
			'timeFormat' => 'H:i',
			'show2400' => true,
			'showDuration' => false
		);
		$timepicker_start_args = apply_filters( 'lafrec_timepicker_start_args', $timepicker_start_args );

		$timepicker_end_args = array(
			'lang' => $timepicker_lang,
			'minTime' => '00:00',
			'maxTime' => '23:30',
			'step' => 10,
			'timeFormat' => 'H:i',
			'show2400' => true,
			'showDuration' => true
		);
		$timepicker_end_args = apply_filters( 'lafrec_timepicker_end_args', $timepicker_end_args );

		wp_localize_script(
			'lafrec-admin-post',
			'LAFREC',
			array(
				'endpoint' => lafrec_ajax_url(),
				'messages' => array(
					'fail_ajax_calendar' => __( 'Failed to get calendar data. Try again in a moment.', LAFREC_TEXT_DOMAIN )
				),
				'timepicker' => array(
					'start' => $timepicker_start_args,
					'end' => $timepicker_end_args
				)
			)
		);

		wp_enqueue_style(
			'lafrec-admin',
			LAFREC_PLUGIN_DIR_URL . 'css/admin.css'
		);

	}
}


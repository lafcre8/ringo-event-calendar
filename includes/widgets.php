<?php
/**
 * Widgets API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Register widgets.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_register_widgets() {
	register_widget( 'Lafrec_Widget_Schedule_List' );
}

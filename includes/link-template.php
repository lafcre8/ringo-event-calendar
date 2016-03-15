<?php
/**
 * Link Template Functions.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Retrieve the ajax endpoint url.
 *
 * @since 0.1.0
 *
 * @return void
 */
function lafrec_ajax_url() {
	$url = str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) );
	return apply_filters( 'lafrec_ajax_url', $url );
}

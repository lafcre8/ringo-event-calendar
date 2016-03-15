<?php
/**
 * Translation API.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Load plugin language files.
 *
 * @since 0.1.0
 *
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function lafrec_load_textdomain() {
	return load_plugin_textdomain( LAFREC_TEXT_DOMAIN, false, LAFREC_LANG_DIR );
}

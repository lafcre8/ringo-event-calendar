<?php
/**
 * Option API.
 *
 * @package Lafrec
 */

/**
 * Retrieve option value.
 *
 * Looks to see if the specified setting exists, returns default if not.
 *
 * @since 0.1.0
 *
 * @global array $lafrec_options
 * @param string $key Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed $key Optional. Default value to return if the option does not exist.
 * @return mixed Value set for the option.
 */
function lafrec_get_option( $key, $default = false ) {
	global $lafrec_options;

  $key = trim( $key );
	if ( empty( $key ) ) {
		return false;
	}

	if ( isset( $lafrec_options[$key] ) ) {
		$value = $lafrec_options[$key];
	} else {
		$value = $default;
	}

	$value = apply_filters( 'lafrec_get_option', $value, $key, $default );
	return apply_filters( 'lafrec_get_option_' . $key, $value, $key, $default );

}

/**
 * Update an option.
 *
 * Updates an plugin settings value in both the database and the global variable.
 *
 * @since 0.1.0
 *
 * @global array $lafrec_options
 * @param string $key Option name. Expected to not be SQL-escaped.
 * @param mixed $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 * @return bool False if value was not updated and true if value was updated.
 */
function lafrec_update_option( $key, $value ) {
  $key = trim( $key );
	if ( empty( $key ) ) {
		return false;
	}

	$options = get_option( 'lafrec_options' );
	$value = apply_filters( 'lafrec_update_option', $value, $key );
	$options[$key] = $value;
	$did_update = update_option( 'lafrec_options', $options );

	if ( $did_update ) {
		global $lafrec_options;
		$lafrec_options[$key] = $value;
	}

	return $did_update;
}

/**
 * Update an option.
 *
 * Updates an plugin settings value in both the database and the global variable.
 *
 * @since 0.1.0
 *
 * @global array $lafrec_options
 * @param string $key Option name. Expected to not be SQL-escaped.
 * @param mixed $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 * @return bool False if value was not updated and true if value was updated.
 */
function lafrec_delete_option( $key ) {
  $key = trim( $key );
	if ( empty( $key ) ) {
		return false;
	}

	$options = get_option( 'lafrec_options' );
	if ( isset( $options[$key] ) ) {
		unset( $options[$key] );
	}

	$did_update = update_option( 'lafrec_options', $options );

	if ( $did_update ) {
		global $lafrec_options;
		$lafrec_options = $options;
	}

	return $did_update;
}

/**
 * Retrieve all plugin settings.
 *
 * @since 0.1.0
 *
 * @return array Plugin settings.
 */
function lafrec_load_options() {
	global $lafrec_options;

	$options = get_option( 'lafrec_options' );
	if ( empty( $options ) ) {
		$options = array();
		add_option( 'lafrec_options', $options, '', 'yes' );
	}
	$lafrec_options = $options;

	return $options;
}

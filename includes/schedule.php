<?php
/**
 * Event Schedule API.
 *
 * @package Lafrec
 */

/**
 * Return schedules table colmun formats.
 *
 * @since 0.1.0

 * @return array Column formats.
 */
function lafrec_get_schedules_column_formats() {
	return array(
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
}

/**
 * Return schedules table name with prefix.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return string Name of table.
 */
function lafrec_get_schedules_table_name() {
	global $wpdb;

	$table = $wpdb->prefix . 'lafrec_schedules';
	return $table;
}

/**
 * Retrieve a schedule from database.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $id ID to retrieve schedule.
 * @return Lafrec_Schedule|null Shcedule object on success. null on failure.
 */
function lafrec_get_schedule( $id ) {
	global $wpdb;

	$id = (int) $id;

	$table = lafrec_get_schedules_table_name();
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) );
	if ( empty( $row ) ) {
		return null;
	}

	$start = new DateTime( $row->start_date . ' ' . $row->start_time );
	$end = new DateTime( $row->end_date . ' ' . $row->end_time );
	$schedule = new Lafrec_Schedule( $start, $end );
	$schedule->id = $row->id;
	$schedule->event_post = get_post( (int) $row->event_post_id );
	$schedule->created_at = new DateTime( $row->created_at );
	$schedule->updated_at = new DateTime( $row->updated_at );

	return $schedule;
}


/**
 * Retrieve schedules from database.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $args Optional. Arguments to retrieve schedules.
 * @return array|WP_Error List of shcedules on success. WP_Error on failure.
 */
function lafrec_get_schedules( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'from' => date_i18n( 'Y-m-d', LAFREC_CURRENT_TIME ),
		'to' => '',
		'limit' => 10,
		'event_post_type' => lafrec_get_post_types(),
		'event_post_id' => 0,
		'event_post_status' => 'publish',
		'single_schedule' => false,
		'order' => 'ASC'
	);
	$args = wp_parse_args( $args, $defaults );

	/**
	 * Build base DML
	 */
	$table = lafrec_get_schedules_table_name();

	$sql = "SELECT ";
	$sql .= " s.id, s.event_post_id, s.start_date, s.start_time, s.end_date, s.end_time, s.created_at, s.updated_at,";
	$sql .= " p.post_title";
	$sql .= " FROM {$table} as s";
	$sql .= " INNER JOIN {$wpdb->posts} as p";
	$sql .= " ON s.event_post_id = p.ID";

	/**
	 * Build WHERE closure
	 */
	$where = ' WHERE 1 = 1 ';
	$where = " AND s.status = 'open' ";

	// Post status
	$post_status = $args[ 'event_post_status' ];
	if ( ! empty( $post_status ) ) {
		if ( is_array( $post_status ) ) {
			$post_status = array_map( 'trim', $post_status );
			$post_status_count = count( $post_status );
			$post_status_placeholder = array_fill( 0, $post_status_count, '%s' );
			$post_status_placeholder = implode( ', ', $post_status_placeholder );
			$where .= $wpdb->prepare( ' AND p.post_status IN (' . $post_status_placeholder . ') ', $post_status );
		} else if ( is_string( $post_status ) && $post_status !== 'any' ) {
			$where .= $wpdb->prepare( ' AND p.post_status = %s ', $post_status );
		}
	}

	// Date range
	if ( ! empty( $args['from'] ) ) {
		$from = lafrec_parse_date( $args['from'] );
		$where .= $wpdb->prepare( ' AND s.start_date >= %s ', $from->format( 'Y-m-d' ) );
	}

	if ( ! empty( $args['to'] ) ) {
		$to = lafrec_parse_date( $args['to'] );
		$where .= $wpdb->prepare( ' AND s.start_date <= %s ', $to->format( 'Y-m-d' ) );
	}

	// Post type condition
	$post_type = $args[ 'event_post_type' ];
	if ( ! empty( $post_type ) ) {
		if ( is_array( $post_type ) ) {
			$post_type = array_map( 'trim', $post_type );
			$post_type_count = count( $post_type );
			$post_type_placeholder = array_fill( 0, $post_type_count, '%s' );
			$post_type_placeholder = implode( ', ', $post_type_placeholder );
			$where .= $wpdb->prepare( ' AND p.post_type IN (' . $post_type_placeholder . ') ', $post_type );
		} else {
			$where .= $wpdb->prepare( ' AND p.post_type = %s ', trim( $post_type ) );
		}
	}

	// Post id condition
	$post_id = $args[ 'event_post_id' ];
	if ( ! empty( $post_id ) ) {
		if ( is_array( $post_id ) ) {
			$post_id = array_map( 'absint', $post_id );
			$post_id_count = count( $post_id );
			$post_id_placeholder = array_fill( 0, $post_id_count, '%d' );
			$post_id_placeholder = implode( ', ', $post_id_placeholder );

			$where .= $wpdb->prepare( ' AND s.event_post_id IN (' . $post_id_placeholder . ') ', $post_id );
		} else {
			$where .= $wpdb->prepare( ' AND s.event_post_id = %d ', absint( $post_id ) );
		}
	}

	// Single schedule per event
	$_order = strtoupper( $args[ 'order' ] );
	$single_schedule = (bool) $args[ 'single_schedule' ];
	if ( $single_schedule ) {
		$aggregate = 'MIN';
		if ( $_order === 'DESC' ) {
			$aggregate = 'MAX';
		}
		$where .= " AND CAST( CONCAT( s.start_date, ' ', s.start_time ) as DATE ) ";
		$where .= " IN (";
		$where .= "   SELECT {$aggregate}( CAST( CONCAT( ss.start_date, ' ', ss.start_time) as DATE ) ) ";
		$where .= "   FROM ";
		$where .= "     {$table} as ss ";
		$where .= "   GROUP BY ss.event_post_id, ss.start_date ";
	  $where .= "	) ";
	}

	$sql .= $where;

	/**
	 * Build GROUP BY closure
	 */
	$group_by = '';
	if ( $single_schedule ) {
		$group_by .= " GROUP BY s.event_post_id, s.start_date ";
	}

	$sql .= $group_by;

	/**
	 * Build ORDER BY closure
	 */
	$order_by = '';
	if ( $_order === 'DESC' ) {
		$order_by .= " ORDER BY s.start_date DESC, s.start_time DESC, p.ID ASC";
	} else {
		$order_by .= " ORDER BY s.start_date ASC, s.start_time ASC, p.ID ASC";
	}

	$sql .= $order_by;

	/**
	 * Build LIMIT closure
	 */
	$limit = '';
	$number = (int) $args['limit'];
	if ( $number > 0 ) {
		$limit .= $wpdb->prepare( ' LIMIT %d ', $number );
	}

	$sql .= $limit;

	/**
	 * Execute query
	 */
	$results = $wpdb->get_results( $sql );
	if ( $wpdb->last_error ) {
		$message = __( 'Could not select event schedule into the database', LAFREC_TEXT_DOMAIN );
		return new WP_Error( 'db_select_error', $message, $wpdb->last_error );
	}

	/**
	 * Create Schedule objects and return it
	 */
	$schedules = array();
	foreach ( $results as $row ) {
		$start = new DateTime( $row->start_date . ' ' . $row->start_time );
		$end = new DateTime( $row->end_date . ' ' . $row->end_time );

		$schedule = new Lafrec_Schedule( $start, $end );
		$schedule->id = $row->id;
		$schedule->event_post = get_post( (int) $row->event_post_id );
		$schedule->created_at = new DateTime( $row->created_at );
		$schedule->updated_at = new DateTime( $row->updated_at );

		$schedules[] = $schedule;
	}

	return $schedules;
}

/**
 * Insert Schedule.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Post ID of event.
 * @param string|DateTime $start Event start date.
 * @param string|DateTime $end Event end date.
 * @return int|WP_Error The number of rows affected on success. WP_Error on failure.
 */
function lafrec_insert_schedule( $data ) {
	global $wpdb;

	$timestamp = current_time( 'mysql' );

	$defaults = array(
		'event_post_id' => null,
		'event_time_number' => null,
		'status' => 'open',
		'start_date' => null,
		'start_time' => '00:00',
		'end_date' => null,
		'end_time' => '23:50',
		'created_at' => $timestamp,
		'updated_at' => $timestamp
	);

	$data = array_change_key_case( $data, CASE_LOWER );
	$data = wp_parse_args( $data, $defaults );

	$formats = lafrec_get_schedules_column_formats();
	$data = array_intersect_key( $data, $formats );

	$data_keys = array_keys( $data );
	$data_formats = array();
	foreach ( $data_keys as $key ) {
		$data_formats[] = $formats[$key];
	}

	$table = lafrec_get_schedules_table_name();
	$result = $wpdb->insert( $table, $data, $data_formats );
	if ( $result === false ) {
		$message = __( 'Could not insert event schedule into the database', LAFREC_TEXT_DOMAIN );
		return new WP_Error( 'db_insert_error', $message, $wpdb->last_error );
	}

	return $result;
}

/**
 * Update Schedule.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $data Data to update (in column => value pairs).
 * @param array $where A named array of WHERE clauses (in column => value pairs).
 * @return int|WP_Error The number of rows updated on success. WP_Error on failure.
 */
function lafrec_update_schedule( $data, $where ) {
	global $wpdb;

	$formats = lafrec_get_schedules_column_formats();

	// Setup data
	$data = array_change_key_case( $data, CASE_LOWER );
	$data = array_intersect_key( $data, $formats );

	$data_keys = array_keys( $data );
	$data_formats = array();
	foreach ( $data_keys as $key ) {
		$data_formats[] = $formats[$key];
	}

	// Setup WHERE condition
	$where = array_change_key_case( $where, CASE_LOWER );
	$where = array_intersect_key( $where, $formats );

	$where_keys = array_keys( $where );
	$where_formats = array();
	foreach ( $where_keys as $key ) {
		$where_formats[] = $formats[$key];
	}

	$table = lafrec_get_schedules_table_name();
	$result = $wpdb->update( $table, $data, $where, $data_formats, $where_formats );

	if ( $result === false ) {
		$message = __( 'Could not update event schedule into the database', LAFREC_TEXT_DOMAIN );
		return new WP_Error( 'db_update_error', $message, $wpdb->last_error );
	}

	return $result;
}

/**
 * Delete Schedule.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $where A named array of WHERE clauses (in column => value pairs).
 * @return int|WP_Error The number of rows updated on success. WP_Error on failure.
 */
function lafrec_delete_schedule( $where ) {
	global $wpdb;

	if ( ! is_array( $where ) ) {
		return false;
	}

	$formats = lafrec_get_schedules_column_formats();

	$where = array_change_key_case( $where, CASE_LOWER );
	$where = array_intersect_key( $where, $formats );

	$where_keys = array_keys( $where );
	$where_formats = array();
	foreach ( $where_keys as $key ) {
		$where_formats[] = $formats[$key];
	}

	$table = lafrec_get_schedules_table_name();
	$result = $wpdb->delete( $table, $where, $where_formats );

	if ( $result === false ) {
		$message = __( 'Could not delete event schedule into the database', LAFREC_TEXT_DOMAIN );
		return new WP_Error( 'db_delete_error', $message, $wpdb->last_error );
	}

	return $result;
}

/**
 * Delete Schedule by date
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array|DateTime|string $date Value for date condition.
 * @param array $where Optional. A named array of WHERE clauses (in column => value pairs).
 * @return int|false The number of rows affected, or false on error.
 */
function lafrec_delete_by_date( $date, $where = array() ) {
	global $wpdb;

	if ( empty( $date ) ) {
		return false;
	}

	$table = lafrec_get_schedules_table_name();

	$sql = "DELETE";
	$sql .= " FROM {$table}";

	$where_closure = ' WHERE 1 = 1 ';

	if ( is_array( $date ) ) {
		$date_values = array();

		$date = array_filter( $date );
		foreach ( $date as $_date ) {
			if ( $_date instanceof DateTime ) {
				$_date = $_date->format( 'Y-m-d' );
			}
			$date_values[] = (string) $_date;
		}
		$count = count( $date_values );
		$placeholder = array_fill( 0, $count, '%s' );
		$placeholder = implode( ', ', $placeholder );
		$where_closure .= $wpdb->prepare( ' AND start_date IN (' . $placeholder . ') ', $date_values );
	} else {
		if ( $date instanceof DateTime ) {
			$date = $date->format( 'Y-m-d' );
		}
		$where_closure .= $wpdb->prepare( ' AND start_date = %s ', $date );
	}

	if ( ! empty( $where ) && is_array( $where ) ) {
		$formats = lafrec_get_schedules_column_formats();

		$where = array_change_key_case( $where, CASE_LOWER );
		$where = array_intersect_key( $where, $formats );

		foreach ( $where as $column => $condition ) {
			$format = $formats[$column];
			$where_closure .= $wpdb->prepare( ' AND ' . $column . ' = ' . $format . ' ', $condition );
		}
	}

	$sql .= $where_closure;

	$result = $wpdb->query( $sql );
	if ( $result === false ) {
		$message = __( 'Could not delete event schedule into the database', LAFREC_TEXT_DOMAIN );
		return new WP_Error( 'db_delete_error', $message, $wpdb->last_error );
	}

	return $result;
}

/**
 * Delete Schedule. Called by delete_post hook.
 *
 * @since 0.1.0
 *
 * @param int $post_id Post ID of event.
 * @return void|int|WP_Error Void on post type is not event. The number of rows updated on success. WP_Error on failure.
 */
function lafrec_delete_schedule_on_delete_post( $post_id ) {
	$post_type = get_post_type();
	if ( ! in_array( $post_type, lafrec_get_post_types() ) ) {
		return;
	}

	return lafrec_delete_schedule_by_post_id( $post_id );
}

/**
 * Delete Schedule by Post id.
 *
 * @since 0.1.0
 *
 * @param int $post_id Post ID of event.
 * @return int|WP_Error The number of rows updated on success. WP_Error on failure.
 */
function lafrec_delete_schedule_by_post_id( $post_id ) {
	$where = array(
		'event_post_id' => $post_id
	);
	return lafrec_delete_schedule( $where );
}

/**
 * Check Schedules table exists.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return bool True if table exists, false if table not exists.
 */
function lafrec_exists_schedules_table() {
	global $wpdb;

	$table = lafrec_get_schedules_table_name();
	$exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $table ) ) === $table;

	return $exists;
}

/**
 * Create Schedules table.
 *
 * @since 0.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return void
 */
function lafrec_create_schedules_table() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table = lafrec_get_schedules_table_name();

	$ddl = "CREATE TABLE `{$table}` (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_post_id bigint(20) UNSIGNED NOT NULL,
		event_time_number bigint(20) UNSIGNED NOT NULL,
		status varchar(20) NOT NULL,
		start_date date NOT NULL,
		start_time time NOT NULL,
		end_date date NOT NULL,
		end_time time NOT NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY id_date_number (event_post_id, start_date, event_time_number)
	) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $ddl );
}

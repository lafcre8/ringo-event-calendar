<?php
/**
 * Schedule list widget class.
 *
 * @package Lafrec
 * @since 0.1.0
 */

/**
 * Implementation for Schedule list widget
 *
 * @since 0.1.0
 */
class Lafrec_Widget_Schedule_List extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'lafrec_schedule_list',
			__( 'Event Schedule List', LAFREC_TEXT_DOMAIN ),
			array(
				'description' => __( 'Your site&#8217;s event shcedules.', LAFREC_TEXT_DOMAIN )
			)
		);
	}

	public function widget( $args, $instance ) {
		$number = $instance['number'];
		$title = $instance['title'];

		$one_year_later = date_i18n( 'Y-m-d', strtotime( '+1 year', current_time( 'timestamp' ) ) );
		$schedule_args = array(
			'from' => date_i18n( 'Y-m-d', LAFREC_CURRENT_TIME ),
			'limit' => $number
		);
		$schedules = lafrec_get_schedules( $schedule_args );

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		echo '<ul>';
		foreach ( $schedules as $schedule ) {
			$post_id = $schedule->event_post->ID;
			$post_title = get_the_title( $post_id );
			$post_link = get_the_permalink( $post_id );
			echo '<li>';
			echo '<div>';
			echo $schedule->start->format( 'Y-m-d H:i' );
			echo '</div>';
			echo '<a href="' . esc_url( $post_link ) . '">' . esc_html( $post_title ) . '</a>';
			echo '</li>';
		}
		echo '</ul>';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', LAFREC_TEXT_DOMAIN ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of schedules to show:', LAFREC_TEXT_DOMAIN ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		return $instance;
	}

}

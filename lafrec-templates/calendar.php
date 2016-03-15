<?php if ( count( $calendar->get_days() > 0 ) ) : ?>
	<div class="lafrec-calendar">
		<table class="lafrec-calendar-table">

		<tr>
			<?php foreach ( $calendar->get_day_names() as $name ): ?>
				<th class="lafrec-calendar-head"><?php echo esc_html( $name ); ?></th>
			<?php endforeach; ?>
		</tr>
		<?php foreach ( $calendar->get_days() as $calendar_day ): ?>
			<?php if ( $calendar_day->equals_day_of_week( $calendar->first_day_of_week ) ) : ?>
				<tr>
			<?php endif; ?>

			<td class="<?php echo esc_attr( lafrec_get_calendar_day_class( $calendar_day ) ); ?>">
				<?php if ( $calendar_day->equals( $calendar->period_start ) || $calendar_day->is_first_day_of_month() ) : ?>
					<h1 class="lafrec-calendar-day-date"><?php echo $calendar_day->format( _x( 'M j', 'calendar date format', LAFREC_TEXT_DOMAIN ) ); ?></h1>
				<?php else: ?>
					<h1 class="lafrec-calendar-day-date"><?php echo $calendar_day->format( _x( 'j', 'calendar date format', LAFREC_TEXT_DOMAIN ) ); ?></h1>
				<?php endif; ?>
				<div class="lafrec-calendar-day-content">
					<?php if ( $calendar_day->has_schedule() ) : ?>
						<ul>
						<?php foreach ( $calendar_day->get_schedules() as $schedule ) : ?>
							<?php global $post; $post = $schedule->event_post; setup_postdata( $post ); ?>
							<li><?php echo $schedule->start->format( 'H:i' ); ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
						<?php endforeach;  ?>
						<?php wp_reset_postdata(); ?>
						</ul>
					<?php endif; ?>
				</div>
			</td>

			<?php if ( $calendar_day->equals_day_of_week( $calendar->last_day_of_week ) ) : ?>
				</tr>
			<?php endif; ?>

		<?php endforeach; ?>
		</table>
	</div>
<?php endif; ?>

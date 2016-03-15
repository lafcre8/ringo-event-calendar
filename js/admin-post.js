(function( $ ) {

	$.ajaxSetup({
		cache : false,
		type: 'post',
		dataType: 'json',
		url: LAFREC.endpoint
	});

	$(function() {
		/*
		 * Calendar Pager
		 */
		$( document ).on( 'click', '.lafrec-calendar-pager-link', function( event ) {
			event.preventDefault();

			var self = this;

			var year = $( self ).attr( 'data-target-year' );
			var month = $( self ).attr( 'data-target-month' );

			var $loader = $( '.lafrec-calendar-loader' );
			var $calendar = $( '.lafrec-admin-calendar:visible' );

			$loader.height( $calendar.height() );
			$calendar.hide();

			var $calendar_to_show = $( '#lafrec-admin-calendar-' + year + month );

			if ( $calendar_to_show.length > 0 ) {

				$calendar_to_show.fadeIn();

			} else {

				$loader.fadeIn();

				var data = {
					action: 'lafrec_get_admin_calendar',
					nonce: $( '[name="lafrec_ajax_nonce"]' ).val(),
					post_id: $( '[name="lafrec_event_post_id"]' ).val(),
					year: year,
					month: month
				};

				$.ajax({
					data: data
				})
				.done( function( response ) {

					if ( ! response.success ) {
						$loader.hide();
						$calendar.show();
						alert( LAFREC.messages.fail_ajax_calendar );
						return;
					}

					$loader.hide();

					var $calendars = $( '.lafrec-admin-calendars' );
					var html = response.data.calendar_html;
					var $html = $( html ).hide();
					$calendars.append( $html );
					$html.fadeIn();

				})
				.fail( function() {
				})
				.always( function() {

					$loader.hide();

				});

			}
		});

		/*
		 * Calendar manipulation
		 */
		var changed_dates = {};

		var day_selector = '.lafrec-admin-calendar-day';
		var day_check_selector = '.lafrec-admin-calendar-day-check';
		var header_day_selector = '.lafrec-admin-calendar-header-day';
		var header_day_check_selector = '.lafrec-admin-calendar-header-day-check';

		$( document ).on( 'click', day_selector, function( event ) {
			$( this ).find( day_check_selector ).trigger( 'click' );
		});

		$( document ).on( 'click', day_selector + ' label', function( event ) {
			event.stopPropagation();
		});

		$( document ).on( 'click', day_check_selector, function( event ) {
			event.stopPropagation();

			var $self = $( this );
			var date = $self.val();
			var checked = $self.is( ':checked' );

			if ( changed_dates.hasOwnProperty( date ) ) {
				if ( changed_dates[date] !== checked ) {
					delete changed_dates[date];
				}
			} else {
				changed_dates[date] = checked;
			}
			$( '[name="lafrec_changed_dates"]' ).val( JSON.stringify( changed_dates ) );
		});

		$( document ).on( 'click', header_day_selector, function( event ) {
			$( this ).find( header_day_check_selector ).trigger( 'click' );
		});

		$( document ).on( 'click', header_day_check_selector, function( event ) {
			event.stopPropagation();
			event.preventDefault();

			var day = $( this ).attr( 'data-day-of-week' );

			var $target_cells = $( day_check_selector ).filter( ':visible' ).filter( '[data-day-of-week="' + day + '"]' );
			var $clean_cells = $target_cells.filter( ':not(:checked)' );
			if ( $clean_cells.length > 0 ) {
				$clean_cells.trigger( 'click' );
			} else {
				$target_cells.trigger( 'click' );
			}
		});

		/*
		 * Event Time Picker
		 */
		function init_event_timepicker( start_selector, end_selector ) {

			var $start_time = $( start_selector ).timepicker( LAFREC.timepicker.start );
			var $end_time = $( end_selector  ).timepicker( LAFREC.timepicker.end );

			$start_time.on( 'changeTime', function( event ) {
				var $self = $( this );

				if ( $self.timepicker( 'getSecondsFromMidnight' ) === null ) {
					return;
				}

				var index = $start_time.index( this );
				var $end = $end_time.eq( index );

				var start_seconds = $self.timepicker( 'getSecondsFromMidnight' );
				var end_seconds = $end.timepicker( 'getSecondsFromMidnight' );
				if ( end_seconds < start_seconds ) {
					$end.timepicker( 'setTime', $self.timepicker( 'getTime' ) );
				}
				$end.timepicker( 'option', 'minTime', $self.val() );

			});

			$end_time.on( 'changeTime', function( event ) {
				var $self = $( this );

				var index = $end_time.index( this );
				var $start = $start_time.eq( index );

				var start_seconds = $start.timepicker( 'getSecondsFromMidnight' )
				if ( start_seconds === null ) {
					$start.timepicker( 'setTime', $self.timepicker( 'getTime' ) );
				}

			});

			$start_time.trigger( 'changeTime' );
		}
		init_event_timepicker( '.lafrec-event-start-time', '.lafrec-event-end-time' );

		/**
		 * Add Time Button
		 */
		$( '.lafrec-add-event-time-button' ).on( 'click', function( event ) {
			event.preventDefault();

			var row_selector = '.lafrec-event-time-row';
			var last_row_selector = '.lafrec-event-time-row:last';

			if ( $( row_selector ).length >= 5 ) {
				return;
			}

			var $clone = $( last_row_selector ).clone().insertAfter( last_row_selector );
			$clone.find( '.lafrec-remove-event-time-button' ).css( 'visibility', 'visible' );

			var number = $( row_selector ).index( $clone ) + 1;
			$clone.attr( 'data-number', number );
			$clone.find( '.lafrec-event-time-number' ).text( number );

			init_event_timepicker( '.lafrec-event-start-time:last', '.lafrec-event-end-time:last' );

		});

		/**
		 * Remove Time Button
		 */
		$( document ).on( 'click', '.lafrec-remove-event-time-button', function( event ) {
			event.preventDefault();
			var $self = $( this );

			var row_selector = '.lafrec-event-time-row';
			var $row = $self.parents( row_selector );
			$row.remove();

			$( row_selector ).each( function() {

				var number = $( row_selector ).index( this ) + 1;
				$( this ).attr( 'data-number', number );
				$( this ).find( '.lafrec-event-time-number' ).text( number );

			});

		});

	}); // Document ready

})(jQuery);

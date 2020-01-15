<?php
	/* Front end view functions */
	function uwa_spoondrift_list_buoys() {
		global $wpdb;
		global $uwa_buoy_details; 

		// Get list of Buoys
		$buoys = $wpdb->get_results(" SELECT * FROM `{$wpdb->prefix}buoy_info` WHERE `visible` = 1 AND `buoy_type` = 'spoondrift'");
		
		foreach($buoys as $b) {
			$html = '';
			
			$recent = $wpdb->get_row("
				SELECT * FROM 
				(SELECT * FROM `wp_spoondrift_post_data_processed` WHERE `spotter_id` = '" . $b->buoy_id . "' ORDER BY id DESC LIMIT 1) AS P
				LEFT JOIN `wp_spoondrift_post_data_processed_waves` AS W
				ON P.`id` = W.`post_data_processed_id`
				ORDER BY W.`timestamp` DESC
				LIMIT 1
			");

			// Check for cached chart
			$recent_option = get_option('spoondrift_recent_event_' . $b->buoy_id, 0);
			if($recent_option == strtotime($recent->timestamp) && !isset($_GET['flush_charts'])) {
				// Grab Cached Version
        $cached = get_option('spoondrift_recent_cache_' . $b->buoy_id, '<p>No cached version available</p>');
				print $cached;
			}
			else {
				// Create new chart
        update_option('spoondrift_recent_event_' . $b->buoy_id, strtotime($recent->timestamp));
						
				$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
					
				$last_observation = "";
				if($recent) {
					// Adjust time from GMT to current time
					if(!$offset = get_option('uwa_spoondrift_time_adjustment')) {
						$offset = '+8'; // Default to Perth +8 hours
					}
					$date = date('d M, H:i', strtotime($offset . ' hours', strtotime($recent->timestamp))); // Hours
					// $date = date('d M, H:i', strtotime($recent->timestamp)); // Hours
					
					// Add Alert if in excess of 90 minutes
					$alert = (strtotime('-90 minutes') > strtotime($recent->timestamp)) ? 'warning' : '';
					$last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . "</span>";
				}
				
				$hide_location = ($b->hide_location === "1") ? true : false;
				
				$html .= '<div class="panel-heading clearfix">
					<h5 style="float: left;">' . $b->title . ' &mdash; ';
					$html .= (!$hide_location) ? '[' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;] &mdash; ' : '';
					$html .= $last_observation . '</h5>
					<a style="float: right;" href="/spoondrift?spotter_id=' . $b->buoy_id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>
				</div>';
				$html .= '<div class="panel-body">';
				
					$html .= '<div class="panel-body">';
						if($recent) {
							// Get previous 3 days wave height, direction and period data.
							$wave_from = $recent->timestamp;
							$wave_until = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($recent->timestamp)));

							$waves = $wpdb->get_results(
								$wpdb->prepare("	
									SELECT * FROM 
									(SELECT * FROM `{$wpdb->prefix}spoondrift_post_data_processed` WHERE spotter_id = '%s') AS P
									INNER JOIN
									(SELECT * FROM `{$wpdb->prefix}spoondrift_post_data_processed_waves` WHERE `timestamp` < '%s' AND `timestamp` > '%s') AS W
									ON P.id = W.post_data_processed_id
									ORDER BY W.`timestamp`
									", 
									$b->buoy_id,
									$wave_from,
									$wave_until
								)
							);
							
							$spotter_id = str_replace('-', '_', sanitize_title($b->buoy_id));
							$chart_id = $spotter_id . '_chart_div';
							$callback = $spotter_id . 'DrawChart';
								
							$direction_points = array();
							foreach($waves as $k => $w) {
								$direction_points[] = ((floor($w->mean_direction / 10) * 10) + 180) % 360; // Flip direction
							}
							
							// Date GMT Offset
							if($offset = get_option('uwa_spoondrift_time_adjustment')) {
								$offset = floatval(str_replace('+', '', $offset));
								$offset = $offset * 3600; // To seconds
							}
							else {
								$offset = 28800;
							}
							
							$data_points = ''; $max_wave = 0; $max_peak = 0; $time_previous = 0;
							foreach($waves as $w) {
								// if($time_previous < strtotime($w->timestamp)) {
									$max_wave = ($w->significant_wave_height > $max_wave) ? $w->significant_wave_height : $max_wave;
									$max_peak = ($w->peak_period > $max_peak) ? $w->peak_period : $max_peak;
									$label = date('M d, Y G:i', strtotime($w->timestamp) + $offset) . '\nSignificant Wave Height: ' . $w->significant_wave_height . ' m\nPeak Period: ' . $w->peak_period . ' s';
									
									$time = strtotime($w->timestamp) + $offset; // 28800; // Add 8 Hours
									$data_points .= '[new Date(' . $time . ' * 1000), ' . $w->significant_wave_height . ', "' . $label . '", ' . $w->peak_period . ', "' . $label . '"],';
									//$time_previous = strtotime($w->timestamp);
								// }
							}
							$max_wave = round($max_wave * 2);
							$max_peak = $max_peak + 3;

							$html .= generate_google_chart_with_args(
								array(
									'bouy_id' => $spotter_id, 
									'chart_id' => $chart_id, 
									'callback' => $callback, 
									'data_points' => $data_points, 
									'max_wave' => $max_wave, 
									'max_peak' => $max_peak, 
									'direction_points' => $direction_points, 
									'modulus' => 2, 
									'return' => true
								)
							);

							$html .= '<table class="table">';
								$html .= '<thead><tr>';
									$html .= '<th>Significant Wave Height</th>';
									$html .= '<th>Peak Period</th>';
									$html .= '<th>Peak Direction</th>';
									$html .= '<th>Directional spreading</th>';
								$html .= '</tr></thead>';
								$html .= '<tbody>';
									$html .= '<tr>';
										$html .= '<td><strong>' . $recent->significant_wave_height . ' m</strong></td>';
										$html .= '<td>' . $recent->peak_period . ' s</td>';
										$html .= '<td>' . $recent->peak_direction . ' degrees</td>';
										$html .= '<td>' . $recent->peak_directional_spread . ' degrees</td>';
									$html .= '</tr>';
								$html .= '</tbody>';
							$html .= '</table>';
						}
					$html .= '</div>';
				$html .= '</div>';
				
				$return = '<div class="panel panel-primary buoy-' . $b->buoy_id . '">' . $html . '</div>';
				print $return;

				// Save for Caching
        update_option('spoondrift_recent_cache_' . $b->buoy_id, '<div class="panel panel-primary buoy-' . $b->buoy_id . ' cached">' . $html . '</div>');
			}
		}		
	}

	function uwa_spoondrift_model_results_with_args($args) {
		$defaults = array(
			'buoy_id' => '', 
			'from_date' => null, 
			'until_date' => null,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);

		$buoy_id = $_args['buoy_id'];
		$from_date = $_args['from_date'];
		$until_date = $_args['until_date'];

		$html = '';

		global $wpdb, $uwa_photo_lookup, $uwa_buoy_details;	

		$title = (isset($uwa_buoy_details[$buoy_id])) ? $uwa_buoy_details[$buoy_id]['title'] : $buoy_id;
		
		$recent = $wpdb->get_row(
			$wpdb->prepare("
				SELECT * FROM {$wpdb->prefix}spoondrift_post_data_processed_waves AS w 
				LEFT JOIN {$wpdb->prefix}spoondrift_post_data_processed AS p 
				ON p.id = w.post_data_processed_id 
				WHERE `spotter_id` = '%s' 
				LIMIT 1;
	  	",
	  	$buoy_id)
	  );
		
		$html .= '<div class="panel panel-primary">';
			$html .= '<div class="panel-heading panel-memplot">' . $title .' Wave Buoy</div>';
			$html .= '<div class="panel-body">';
			
				$until_where = '';
				if($until != null) {
					$until_where = " AND timestamp <= '" . $until . "' ";
				}
				
				// Get most recent results
				$memplot = $wpdb->get_row(
					$wpdb->prepare("SELECT * FROM `{$wpdb->prefix}spoondrift_memplot` 
						WHERE buoy_id='%s' 
						" . $until_where . "
						ORDER BY timestamp 
						DESC LIMIT 1
					", $buoy_id)
				);
			
				if($memplot) {
					$date = date('d-m-Y H:i:s', strtotime($memplot->timestamp));
					$html .= '<div class="col-sm-7">';
						$html .= '<h5 style="text-align: center;" class="panel-memplot">Frequency-Directional Spectrum</h5>';

						$html .= '<div class="memplot">
							<img src="/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=image_fetch&key=' . $memplot->url . '" alt="">
							<input type="hidden" name="memplot" value="' . $memplot->url . '">
						</div>';
					$html .= '</div>';
				}
			
				$html .= '<div class="col-sm-5">';
					$html .= '<div>';
					if(isset($uwa_buoy_details[$buoy_id])) {
						if(isset($uwa_buoy_details[$buoy_id]['description'])) {
							$html .= $uwa_buoy_details[$buoy_id]['description'];
						}
						
						if(!$uwa_buoy_details[$buoy_id]['hide_location']) {
							$html .= '<br>Position: ' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;';	
						}
						if(isset($uwa_buoy_details[$buoy_id]['depth']) && !empty($uwa_buoy_details[$buoy_id]['depth'])) {
							$html .= '<br>Approx water depth: ' . $uwa_buoy_details[$buoy_id]['depth'];
						}
					}
					$html .= '</div>';
					if(isset($uwa_photo_lookup[$buoy_id]) && !empty($uwa_photo_lookup[$buoy_id])) {
						$html .= '<img src="' . $uwa_photo_lookup[$buoy_id] . '" class="img-thumbnail" style="">';
					}
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		if($_args['return']) {
			return $html;
		}
		print $html;
	}

	function uwa_spoondrift_time_series_csv($spotter_id = '', $from_date = null, $until_date = null) {
		if($spotter_id == '') {
			return;
		}
		
		global $wpdb;

		if($from_date == null) {
			$from_date = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7)));
		}
		if($until_date == null) {
			$until_date = Date('Y-m-d H:i:s', time());
		}

		$data = $wpdb->get_results(
			$wpdb->prepare( "SELECT * 
				FROM `wp_spoondrift_post_data_processed` AS P 
				LEFT JOIN `wp_spoondrift_post_data_processed_waves` AS W 
				ON P.`id` = W.`post_data_processed_id` 
				WHERE P.`spotter_id` = '%s' 
				AND W.`timestamp` >= '%s'
				AND W.`timestamp` <= '%s'
				ORDER BY W.`timestamp` ASC",
				$spotter_id,
				$from_date,
				$until_date
			)
		);
		
		uwa_sql_results_to_table($data, $spotter_id);
	}

	function uwa_spoondrift_time_series_data_with_args($args) {
		$html = '';
				
		$defaults = array(
			'buoy_id' => '', 
			'from_date' => null, 
			'until_date' => null,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);

		$spotter_id = $_args['buoy_id'];
		$from_date = $_args['from_date'];
		$until_date = $_args['until_date'];

		if($spotter_id == '') {
			return $html;
		}

		global $wpdb;
		global $uwa_buoy_details;

		if($from_date == null) {
			$from_date = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7 * 8)));
		}
		if($until_date == null) {
			$until_date = Date('Y-m-d H:i:s', time());
		}

		$data = $wpdb->get_results(
				$wpdb->prepare( "SELECT * 
					FROM `wp_spoondrift_post_data_processed` AS P 
					LEFT JOIN `wp_spoondrift_post_data_processed_waves` AS W 
					ON P.`id` = W.`post_data_processed_id` 
					WHERE P.`spotter_id` = '%s' 
					AND W.`timestamp` >= '%s'
					AND W.`timestamp` <= '%s'
					ORDER BY W.`timestamp` ASC",
					$spotter_id,
					$from_date,
					$until_date
				)
		);

		if($data) {
			$sig_wave_x = array();
			$sig_wave_y = array();

			$peak_period_x = array();
			$peak_period_y = array();

			$mean_period_x = array();
			$mean_period_y = array();

			$peak_direction_x = array();
			$peak_direction_y = array();

			$peak_directional_spread_x = array();
			$peak_directional_spread_y = array();

			$mean_direction_x = array();
			$mean_direction_y = array();

			$mean_directional_spread_x = array();
			$mean_directional_spread_y = array();

			foreach($data as $d) {
				if(!$offset = get_option('uwa_spoondrift_time_adjustment')) {
					$offset = '+8'; // Default to Perth +8 hours
				}
				$timestamp = date('Y-m-d H:i:s', strtotime($offset . ' hours', strtotime($d->timestamp)));
				// Significant Wave Heig
				if($d->significant_wave_height > 0) {
					array_push($sig_wave_x, $timestamp);
					array_push($sig_wave_y, $d->significant_wave_height);
				}
				
				// // Peak Period
				if($d->peak_period > 0) {
					array_push($peak_period_x, $timestamp);
					array_push($peak_period_y, $d->peak_period);
				}
				
				// // Mean Period
				if($d->mean_period > 0) {
					array_push($mean_period_x, $timestamp);
					array_push($mean_period_y, $d->mean_period);
				}
				
				// // Peak Direction, Peak Spr
				if($d->peak_direction > 0) {
					array_push($peak_direction_x, $timestamp);
					array_push($peak_direction_y, $d->peak_direction);
				}
				
				if($d->peak_directional_spread > 0) {
					array_push($peak_directional_spread_x, $timestamp);
					array_push($peak_directional_spread_y, $d->peak_directional_spread);
				}
				
				// // Mean Direction, Mean Spr
				if($d->mean_direction > 0) {
					array_push($mean_direction_x, $timestamp);
					array_push($mean_direction_y, $d->mean_direction);
				}
				
				if($d->mean_directional_spread > 0) {
					array_push($mean_directional_spread_x, $timestamp);
					array_push($mean_directional_spread_y, $d->mean_directional_spread);
				}

			}

			$title = (isset($uwa_buoy_details[$data[0]->spotter_id])) ? $uwa_buoy_details[$data[0]->spotter_id]['title'] : $data[0]->spotter_id;

			$html .= '<div class="panel panel-primary">';
				$html .= '<div class="panel-heading">' . $title . ' (Spoondrift) &mdash; Time Series Data<br>( ' . date('d-m-Y', strtotime($from_date)) . '  &mdash; ' . date('d-m-Y', strtotime($until_date)) . ' )</div>';

				$html .= '<div class="panel-body">';
					$html .= generate_time_series_charts($spotter_id, $sig_wave_x, $sig_wave_y, array(), array(), $peak_period_x, $peak_period_y, $mean_period_x, $mean_period_y, $peak_direction_x, $peak_direction_y, $peak_directional_spread_x, $peak_directional_spread_y, $mean_direction_x, $mean_direction_y, $mean_directional_spread_x, $mean_directional_spread_y, false, true);
				$html .= '</div>';
			$html .= '</div>';
		}
		else {
			$html .= '<div class="panel panel-primary">';
				$html .= '<div class="panel-body">';
					$html .= 'No data in this date range';
				$html .= '</div>';
			$html .= '</div>';
		}

		$from_date_month = date('m', strtotime($from_date)) - 1;
		$until_date_month = date('m', strtotime($until_date)) - 1;

		$html .= '<script type="text/javascript">
			/*
		  ** Refine Dates on Single Buoy Data
		  */
		  var start = moment().set({
		  		\'year\': ' . date('Y', strtotime($from_date)) . ',
		  		\'month\': ' . $from_date_month . ', // JS Month starts at zero
		  		\'date\': ' . date('d', strtotime($from_date)) . '
		  	});
		  var end = moment().set({
					\'year\': ' . date('Y', strtotime($until_date)) . ',
		  		\'month\': ' . $until_date_month . ', // JS Month starts at zero
		  		\'date\': ' . date('d', strtotime($until_date)) . '
		  	});

		  $("input[name=\'dates\']").daterangepicker({
		  	startDate: start,
		  	endDate: end,
		  	locale: {
		      format: \'DD/MM/YYYY\'
		    }
		  });
		  
		  $("input[name=\'dates\']").on("apply.daterangepicker", function(ev, picker) {
			  $(".refine-dates form").first().submit();
			});
		</script>';

		if($_args['return']) {
			return $html;
		}
		print $html;
	}


	// function uwa_spoondrift_time_series_data($spotter_id = '', $from_date = null, $until_date = null) {
	// 	if($spotter_id == '') {
	// 		return;
	// 	}

	// 	global $wpdb;
	// 	global $uwa_buoy_details;

	// 	if($from_date == null) {
	// 		$from_date = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7 * 8)));
	// 	}
	// 	if($until_date == null) {
	// 		$until_date = Date('Y-m-d H:i:s', time());
	// 	}

	// 	$data = $wpdb->get_results(
	// 			$wpdb->prepare( "SELECT * 
	// 				FROM `wp_spoondrift_post_data_processed` AS P 
	// 				LEFT JOIN `wp_spoondrift_post_data_processed_waves` AS W 
	// 				ON P.`id` = W.`post_data_processed_id` 
	// 				WHERE P.`spotter_id` = '%s' 
	// 				AND W.`timestamp` >= '%s'
	// 				AND W.`timestamp` <= '%s'
	// 				ORDER BY W.`timestamp` ASC",
	// 				$spotter_id,
	// 				$from_date,
	// 				$until_date
	// 			)
	// 	);



	// 	if($data) {
	// 		$sig_wave_x = array();
	// 		$sig_wave_y = array();

	// 		$peak_period_x = array();
	// 		$peak_period_y = array();

	// 		$mean_period_x = array();
	// 		$mean_period_y = array();

	// 		$peak_direction_x = array();
	// 		$peak_direction_y = array();

	// 		$peak_directional_spread_x = array();
	// 		$peak_directional_spread_y = array();

	// 		$mean_direction_x = array();
	// 		$mean_direction_y = array();

	// 		$mean_directional_spread_x = array();
	// 		$mean_directional_spread_y = array();

	// 		foreach($data as $d) {
	// 			if(!$offset = get_option('uwa_spoondrift_time_adjustment')) {
	// 				$offset = '+8'; // Default to Perth +8 hours
	// 			}
	// 			$timestamp = date('Y-m-d H:i:s', strtotime($offset . ' hours', strtotime($d->timestamp)));
	// 			// Significant Wave Heig
	// 			if($d->significant_wave_height > 0) {
	// 				array_push($sig_wave_x, $timestamp);
	// 				array_push($sig_wave_y, $d->significant_wave_height);
	// 			}
				
	// 			// // Peak Period
	// 			if($d->peak_period > 0) {
	// 				array_push($peak_period_x, $timestamp);
	// 				array_push($peak_period_y, $d->peak_period);
	// 			}
				
	// 			// // Mean Period
	// 			if($d->mean_period > 0) {
	// 				array_push($mean_period_x, $timestamp);
	// 				array_push($mean_period_y, $d->mean_period);
	// 			}
				
	// 			// // Peak Direction, Peak Spr
	// 			if($d->peak_direction > 0) {
	// 				array_push($peak_direction_x, $timestamp);
	// 				array_push($peak_direction_y, $d->peak_direction);
	// 			}
				
	// 			if($d->peak_directional_spread > 0) {
	// 				array_push($peak_directional_spread_x, $timestamp);
	// 				array_push($peak_directional_spread_y, $d->peak_directional_spread);
	// 			}
				
	// 			// // Mean Direction, Mean Spr
	// 			if($d->mean_direction > 0) {
	// 				array_push($mean_direction_x, $timestamp);
	// 				array_push($mean_direction_y, $d->mean_direction);
	// 			}
				
	// 			if($d->mean_directional_spread > 0) {
	// 				array_push($mean_directional_spread_x, $timestamp);
	// 				array_push($mean_directional_spread_y, $d->mean_directional_spread);
	// 			}

	// 		}

	// 		$title = (isset($uwa_buoy_details[$data[0]->spotter_id])) ? $uwa_buoy_details[$data[0]->spotter_id]['title'] : $data[0]->spotter_id;

	// 		print '<div class="panel panel-primary">';
	// 			print '<div class="panel-heading">' . $title . ' (Spoondrift) &mdash; Time Series Data<br>( ' . date('d-m-Y', strtotime($from_date)) . '  &mdash; ' . date('d-m-Y', strtotime($until_date)) . ' )</div>';

	// 			print '<div class="panel-body">';
	// 				generate_time_series_charts($spotter_id, $sig_wave_x, $sig_wave_y, array(), array(), $peak_period_x, $peak_period_y, $mean_period_x, $mean_period_y, $peak_direction_x, $peak_direction_y, $peak_directional_spread_x, $peak_directional_spread_y, $mean_direction_x, $mean_direction_y, $mean_directional_spread_x, $mean_directional_spread_y, false);
	// 			print '</div>';
	// 		print '</div>';
	// 	}
	// 	else {
	// 		print '<div class="panel panel-primary">';
	// 			print '<div class="panel-body">';
	// 				print 'No data in this date range';
	// 			print '</div>';
	// 		print '</div>';
	// 	}

	// 	$from_date_month = date('m', strtotime($from_date)) - 1;
	// 	$until_date_month = date('m', strtotime($until_date)) - 1;

	// 	print '<script type="text/javascript">
	// 		/*
	// 	  ** Refine Dates on Single Buoy Data
	// 	  */
	// 	  var start = moment().set({
	// 	  		\'year\': ' . date('Y', strtotime($from_date)) . ',
	// 	  		\'month\': ' . $from_date_month . ', // JS Month starts at zero
	// 	  		\'date\': ' . date('d', strtotime($from_date)) . '
	// 	  	});
	// 	  var end = moment().set({
	// 				\'year\': ' . date('Y', strtotime($until_date)) . ',
	// 	  		\'month\': ' . $until_date_month . ', // JS Month starts at zero
	// 	  		\'date\': ' . date('d', strtotime($until_date)) . '
	// 	  	});

	// 	  $("input[name=\'dates\']").daterangepicker({
	// 	  	startDate: start,
	// 	  	endDate: end,
	// 	  	locale: {
	// 	      format: \'DD/MM/YYYY\'
	// 	    }
	// 	  });
		  
	// 	  $("input[name=\'dates\']").on("apply.daterangepicker", function(ev, picker) {
	// 		  $(".refine-dates form").first().submit();
	// 		});
	// 	</script>';
	// }

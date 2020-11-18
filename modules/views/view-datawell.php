<?php 

	function uwa_datawell_list_buoys() {
		global $wpdb;
		global $uwa_buoy_details; 

		// Get list of Buoys
		$buoys = $wpdb->get_results("
			SELECT * 
			FROM `{$wpdb->prefix}buoy_info` 
			WHERE `visible` = 1 
			AND `visibility_options` != 1
			AND `buoy_type` = 'datawell'
		");

		$html_buoys = array();
		
		foreach($buoys as $b) {
			$html = '';
			
			// Most recent
			$recent = $wpdb->get_row(
				$wpdb->prepare("
					SELECT * FROM `{$wpdb->prefix}datawell_post_data_processed_waves`
					WHERE `buoy_id` = '%s'
					AND `significant_wave_height` != 0
					ORDER BY `timestamp` DESC
					LIMIT 1
				",
				$b->buoy_id)
			);

			// Get offset
      $uwa_datawell_time_adjustment = get_option('uwa_datawell_time_adjustment', '+8');

			// Check for cached chart
			$recent_option = get_option('datawell_recent_event_' . $b->buoy_id, 0);
			if($recent_option == strtotime($recent->timestamp) && !isset($_GET['flush_charts'])) {
				// Grab Cached Version
				$cached = get_option('datawell_recent_cache_' . $b->buoy_id, '<p>No cached version available</p>');
				$html_buoys[$b->buoy_order . '-' . $b->buoy_id] = $cached;
			}
			else {
				// Create new chart
				update_option('datawell_recent_event_' . $b->buoy_id, strtotime($recent->timestamp));

				$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
				$last_observation = "";
				if($recent) {
					// $date = date('d M, H:i', strtotime($recent->timestamp) + 28800);
					// $alert = (strtotime('-120 minutes') > strtotime($recent->timestamp)) ? 'warning' : '';
					// $last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . "</span>";
					$recent_time = strtotime($recent->timestamp);
          $recent_time_adjusted = strtotime($uwa_datawell_time_adjustment . ' hours', $recent_time);
          $recent_time_alert = strtotime('-120 minutes', strtotime($uwa_datawell_time_adjustment)); // strtotime('-120 minutes');
          $date = date('d M, H:i', $recent_time_adjusted);
          $alert = ($recent_time_alert > $recent_time) ? 'warning' : '';
          $last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . " (" . $uwa_datawell_time_adjustment . ")</span>";
				}
				
				$hide_location = ($b->hide_location === "1") ? true : false;

				// Grab custom lat/lng if set
				$lat = (!empty($uwa_buoy_details[$b->buoy_id]['custom_lat'])) ? $uwa_buoy_details[$b->buoy_id]['custom_lat'] : $recent->latitude;
				$lng = (!empty($uwa_buoy_details[$b->buoy_id]['custom_lng'])) ? $uwa_buoy_details[$b->buoy_id]['custom_lng'] : $recent->longitude;

				$html .= '<div class="panel-heading clearfix">
					<h5 style="float: left;">' . $title . ' &mdash; ';
					$html .= (!$hide_location) ? '[' . round($lat, 4) . '&deg;, ' . round($lng, 4) . '&deg;] &mdash; ' : '';
					$html .= $last_observation . '</h5>
					<a style="float: right;" href="/datawell?buoy_id=' . $b->buoy_id . '&buoy_info_id=' . $b->id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>
				</div>';
				$html .= '<div class="panel-body">';
			
					
					if($recent) {
						// Get previous 3 days wave height, direction and period data.
						$wave_from = $recent->timestamp;
						$wave_until = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($recent->timestamp))); // 216000sec is 2.5 days

						$html .= '<div class="chart-js-layout" data-buoy="' . $b->buoy_id . '" style="width: 100%;">
							<p class="loading" style="display: none; text-align: center;">Loading&hellip;</p>
							<canvas id="canvas-' . $b->buoy_id . '"></canvas>
						</div>';

						// Fetch Wave Points
						$args = uwa_datawell_wave_points($b, $wave_from, $wave_until, $uwa_datawell_time_adjustment);

						$html .= generate_google_chart_with_args( $args );
						
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
									$recent_peak_direction = (floatval($recent->peak_direction) + $true_north_offset) % 360;
                  $html .= '<td>' . $recent_peak_direction . ' degrees</td>';
									$html .= '<td>' . $recent->peak_directional_spread . ' degrees</td>';
								$html .= '</tr>';
							$html .= '</tbody>';
						$html .= '</table>';
					}
				$html .= '</div>';
				
				$return = '<div class="panel panel-primary buoy-' . $b->buoy_id . '">' . $html . '</div>';
				$html_buoys[$b->buoy_order . '-' . $b->buoy_id] = $return;
				
				// Save for Caching
				update_option('datawell_recent_cache_' . $b->buoy_id, '<div class="panel panel-primary buoy-' . $b->buoy_id . ' cached">' . $html . '</div>');
			}
		}

		return $html_buoys;
	}

	function uwa_datawell_wave_points_json( ) {
		global $wpdb;

		if( isset( $_REQUEST ) ) {
			if( isset( $_REQUEST['buoy_id'] ) &&
					isset( $_REQUEST['wave_from'] ) &&
					isset( $_REQUEST['wave_until'] ) &&
					isset( $_REQUEST['time_adjustment'] ) ) {
				
				$buoy_id = sanitize_text_field( $_REQUEST['buoy_id'] );
				$wave_from = sanitize_text_field( html_entity_decode($_REQUEST['wave_from']) );
				$wave_until = sanitize_text_field( html_entity_decode($_REQUEST['wave_until']) );
				$time_adjustment = sanitize_text_field( $_REQUEST['time_adjustment'] );

				$waves = $wpdb->get_results(
					$wpdb->prepare("
						SELECT *, UNIX_TIMESTAMP(`timestamp`) AS time
						FROM `{$wpdb->prefix}datawell_post_data_processed_waves`
						WHERE `timestamp` < '%s'
						AND `timestamp` > '%s'
						AND (`peak_period` != 0 AND `peak_direction` != 0) # disclude empty values
						AND `buoy_id` = '%s'
						ORDER BY `timestamp` ASC",
						$wave_from,
						$wave_until,
						$buoy_id
					)
				);

				print_r(json_encode($waves));
				
				// $b = $wpdb->get_row(
				// 	$wpdb->prepare("
				// 		SELECT * 
				// 		FROM `{$wpdb->prefix}buoy_info` 
				// 		WHERE `visible` = 1 
				// 		AND `visibility_options` != 1
				// 		AND `buoy_type` = 'datawell'
				// 		AND `buoy_id` = '%s'
				// 	", $buoy_id)
				// );

				// if( $wpdb->num_rows ) {
				// 	print json_encode(uwa_datawell_wave_points($b, $wave_from, $wave_until, $time_adjustment));
				// }
			}
		}

		wp_die();
		// $buoy, $wave_from, $wave_until, $time_adjustment
	}

	add_action( 'wp_ajax_uwa_datawell_wave_points_json', 'uwa_datawell_wave_points_json' );
	add_action( 'wp_ajax_nopriv_uwa_datawell_wave_points_json', 'uwa_datawell_wave_points_json' );

	function uwa_datawell_wave_points($buoy, $wave_from, $wave_until, $time_adjustment) {
		global $wpdb; 

		$waves = $wpdb->get_results(
			$wpdb->prepare("
				SELECT *, UNIX_TIMESTAMP(`timestamp`) AS time
				FROM `{$wpdb->prefix}datawell_post_data_processed_waves`
				WHERE `timestamp` < '%s'
				AND `timestamp` > '%s'
				AND (`peak_period` != 0 AND `peak_direction` != 0) # disclude empty values
				AND `buoy_id` = '%s'
				ORDER BY `timestamp` ASC",
				$wave_from,
				$wave_until,
				$buoy->buoy_id
			)
		);

		$spotter_id = str_replace('-', '_', sanitize_title($buoy->buoy_id));
		$chart_id = $spotter_id . '_chart_div';
		$callback = $spotter_id . 'DrawChart';
		
		$max_wave = 0; 
		$max_peak = 0;
		$data_points = ''; 
		$direction_points = array();
		// True North Offset
		$true_north_offset = (!empty($buoy->true_north_offset)) ? floatval($buoy->true_north_offset) : 0;
		foreach($waves as $k => $w) {
			$direction_points[] = ((floor(($w->peak_direction + $true_north_offset) / 10) * 10) + 180) % 360; // Flip direction 
			
			$max_wave = ($w->significant_wave_height > $max_wave) ? $w->significant_wave_height : $max_wave;
			$max_peak = ($w->peak_period > $max_peak) ? $w->peak_period : $max_peak;
			// Adjust time from GMT using offset
			$wave_time = strtotime($w->timestamp); // GMT
			$adjusted_time = strtotime($time_adjustment . ' hours', $wave_time);
			$label = 'Location: ' . date('M d, Y g:iA', $adjusted_time) . ' (' . $time_adjustment . ')\nGMT: ' . date('M d, Y g:iA', $wave_time) . '\nSignificant Wave Height: ' . $w->significant_wave_height . ' m\nPeak Period: ' . $w->peak_period . ' s';
			$data_points .= '[new Date(' . $wave_time . '000), ' . $w->significant_wave_height . ', "' . $label . '", ' . $w->peak_period . ', "' . $label . '"],';
		}

		$max_wave = round($max_wave * 2);
		$max_peak = round($max_peak) + 2; // floor(round($max_peak) / $max_wave) * $max_wave + $max_wave;

		return array(
			'bouy_id' => $spotter_id, 
			'chart_id' => $chart_id, 
			'callback' => $callback, 
			'data_points' => $data_points, 
			'max_wave' => $max_wave, 
			'max_peak' => $max_peak, 
			'direction_points' => $direction_points, 
			'modulus' => 2, 
			'return' => true
		);
	}
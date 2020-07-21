<?php
	/* Front end view functions */
	function uwa_spoondrift_list_buoys() {
		global $wpdb;
		global $uwa_buoy_details; 

		// Get list of Buoys
		$buoys = $wpdb->get_results("
			SELECT * 
			FROM `{$wpdb->prefix}buoy_info` 
			WHERE `visible` = 1 
			AND `visibility_options` != 1
			AND `buoy_type` = 'spoondrift'
		");

		$html_buoys = array();
		
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

			// Get offset
      $uwa_spoondrift_time_adjustment = get_option('uwa_spoondrift_time_adjustment', '+8');

			// Check for cached chart
			$recent_option = get_option('spoondrift_recent_event_' . $b->buoy_id, 0);
			if($recent_option == strtotime($recent->timestamp) && !isset($_GET['flush_charts'])) {
				// Grab Cached Version
        $cached = get_option('spoondrift_recent_cache_' . $b->buoy_id, '<p>No cached version available</p>');
				$html_buoys[$b->buoy_order . '-' . $b->buoy_id] =  $cached;
			}
			else {
				// Create new chart
        update_option('spoondrift_recent_event_' . $b->buoy_id, strtotime($recent->timestamp));
						
				$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
					
				$last_observation = "";
				if($recent) {
          $recent_time = strtotime($recent->timestamp);
          $recent_time_adjusted = strtotime($uwa_spoondrift_time_adjustment . ' hours', $recent_time);
          $recent_time_alert = strtotime('-120 minutes', strtotime($uwa_spoondrift_time_adjustment)); // strtotime('-120 minutes');
          $date = date('d M, H:i', $recent_time_adjusted);
          $alert = ($recent_time_alert > $recent_time) ? 'warning' : '';
          $last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . " (" . $uwa_spoondrift_time_adjustment . ")</span>";
        }
				
				$hide_location = ($b->hide_location === "1") ? true : false;
				
				$html .= '<div class="panel-heading clearfix">
					<h5 style="float: left;">' . $b->title . ' &mdash; ';
					$html .= (!$hide_location) ? '[' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;] &mdash; ' : '';
					$html .= $last_observation . '</h5>
					<a style="float: right;" href="/spoondrift?spotter_id=' . $b->buoy_id . '&buoy_info_id=' . $b->id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>
				</div>';
				
				$html .= '<div class="panel-body">';
					if($recent) {
						// Get previous 3 days wave height, direction and period data.
						$wave_from = $recent->timestamp;
						$wave_until = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($recent->timestamp)));

						// Spotter ID Lookup
						$spoondrift_lookup_id = uwa_spoondrift_lookup_id($b->buoy_id, '');

						// Recent Waves
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
						
						// Date GMT Offset
						$offset = get_option('uwa_spoondrift_time_adjustment', '+8');
								
						$direction_points = array();
						$data_points = ''; 
						$max_wave = 0; 
						$max_peak = 0; 
						// True North Offset
						$true_north_offset = (!empty($b->true_north_offset)) ? floatval($b->true_north_offset) : 0;
						foreach($waves as $k => $w) {
							$direction_points[] = ((floor(($w->peak_direction + $true_north_offset) / 10) * 10) + 180) % 360; // Flip direction 
							
							$max_wave = ($w->significant_wave_height > $max_wave) ? $w->significant_wave_height : $max_wave;
							$max_peak = ($w->peak_period > $max_peak) ? $w->peak_period : $max_peak;
							// Adjust time from GMT using offset
							$wave_time = strtotime($w->timestamp); // GMT
							$adjusted_time = strtotime($uwa_spoondrift_time_adjustment . ' hours', $wave_time);
							$label = 'Location: ' . date('M d, Y g:iA', $adjusted_time) . ' (' . $uwa_spoondrift_time_adjustment . ')\nGMT: ' . date('M d, Y g:iA', $wave_time) . '\nSignificant Wave Height: ' . $w->significant_wave_height . ' m\nPeak Period: ' . $w->peak_period . ' s';
							$data_points .= '[new Date(' . $wave_time . '000), ' . $w->significant_wave_height . ', "' . $label . '", ' . $w->peak_period . ', "' . $label . '"],';
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

						// Recent Wind
						$wave = $wpdb->get_row(
							$wpdb->prepare("
								SELECT * FROM `{$wpdb->prefix}spoondrift_post_data_processed_wind` 
								WHERE `spoondrift_lookup_id` = %d 
								ORDER BY `timestamp` DESC LIMIT 1
								",
								$spoondrift_lookup_id
							)
						);
						$wind_speed = ($wpdb->num_rows > 0) ? floatval($wave->speed) : '-';
						$direction = ($wpdb->num_rows > 0) ? floatval($wave->direction) % 360 : '-';

						$table_values = array(
							'significant_wave_height' => array('Significant Wave Height', '<strong>' . $recent->significant_wave_height . ' m</strong>'),
							'peak_period' => array('Peak Period', $recent->peak_period . ' s'),
							'peak_direction' => array('Peak Direction', strval((floatval($recent->peak_direction) + $true_north_offset) % 360) . ' degrees'),
							'directional_spreading' => array('Directional spreading', $recent->peak_directional_spread . ' degrees'),
							'wind_speed' => array('Wind Speed', $wind_speed . ' m/s'),
							'wind_direction' => array('Wind Direction', $direction . ' degrees')
						);

						$html .= '<table class="table">';
							$html .= '<thead><tr>';
							foreach($table_values as $k => $v) {
								$html .= '<th class="' . sanitize_title($k) . '">' . uwa_global_get_label($k, $v[0]) . '</th>';
							}
							$html .= '</tr></thead>';
							$html .= '<tbody><tr>';
							foreach($table_values as $k => $v) {
								$html .= '<td>' . $v[1] . '</th>';
							}
							$html .= '</tr></tbody>';
						$html .= '</table>';
					}
				$html .= '</div>';
				
				
				$return = '<div class="panel panel-primary buoy-' . $b->buoy_id . '">' . $html . '</div>';
				$html_buoys[$b->buoy_order . '-' . $b->buoy_id] = $return;

				// Save for Caching
        update_option('spoondrift_recent_cache_' . $b->buoy_id, '<div class="panel panel-primary buoy-' . $b->buoy_id . ' cached">' . $html . '</div>');
			}
		}		
		return $html_buoys;
	}
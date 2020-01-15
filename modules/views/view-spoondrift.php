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
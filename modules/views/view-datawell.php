<?php 

	function uwa_datawell_list_buoys() {
		global $wpdb;
		global $uwa_buoy_details; 

		// Get list of Buoys
		$buoys = $wpdb->get_results(" SELECT `buoy_id` FROM `{$wpdb->prefix}buoy_info` WHERE `visible` = 1 AND `buoy_type` = 'datawell'");
		
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

			// Check for cached chart
			$recent_option = get_option('datawell_recent_event_' . $b->buoy_id, 0);
			if($recent_option == strtotime($recent->timestamp) && !isset($_GET['flush_charts'])) {
				// Grab Cached Version
				$cached = get_option('datawell_recent_cache_' . $b->buoy_id, '<p>No cached version available</p>');
				print $cached;
			}
			else {
				// Create new chart
				update_option('datawell_recent_event_' . $b->buoy_id, strtotime($recent->timestamp));

				$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
				$last_observation = "";
				if($recent) {
					$date = date('d M, H:i', strtotime($recent->timestamp) + 28800);
					$alert = (strtotime('-120 minutes') > strtotime($recent->timestamp)) ? 'warning' : '';
					$last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . "</span>";
				}
				
				$hide_location = ($b->hide_location === "1") ? true : false;

				$html .= '<div class="panel-heading clearfix">
					<h5 style="float: left;">' . $title . ' &mdash; ';
					$html .= (!$hide_location) ? '[' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;] &mdash; ' : '';
					$html .= $last_observation . '</h5>
					<a style="float: right;" href="/datawell?buoy_id=' . $b->buoy_id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>
				</div>';
				$html .= '<div class="panel-body">';
			
					
					if($recent) {
						// Get previous 3 days wave height, direction and period data.
						$wave_from = $recent->timestamp;
						$wave_until = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($recent->timestamp))); // 216000sec is 2.5 days

						$waves = $wpdb->get_results(
							$wpdb->prepare("
								SELECT * FROM (
									SELECT *, UNIX_TIMESTAMP(`timestamp`) AS time
									FROM `{$wpdb->prefix}datawell_post_data_processed_waves`
									WHERE `timestamp` < '%s'
									AND `timestamp` > '%s'
									AND (`peak_period` != 0 AND `peak_direction` != 0) # disclude empty values
									ORDER BY `timestamp` DESC
								) AS S # LIMIT 12",
								$wave_from,
								$wave_until
							)
						);
						
						$callback = sanitize_title($b->buoy_id) . 'DrawChart';
						
						foreach($waves as $k => $w) {
							$direction_points[] = ((floor($w->peak_direction / 10) * 10) + 180) % 360; // Flip direction floor($w->peak_direction / 10) * 10;
						}
						
						$data_points = ''; $max_wave = 0; $max_peak = 0;
						foreach($waves as $w) {
							$max_wave = ($w->significant_wave_height > $max_wave) ? $w->significant_wave_height : $max_wave;
							$max_peak = ($w->peak_period > $max_peak) ? $w->peak_period : $max_peak;
							$label = date('M d, Y G:i', strtotime($w->timestamp) + 28800) . '\nSignificant Wave Height: ' . $w->significant_wave_height . ' m\nPeak Period: ' . $w->peak_period . ' s';
							$time = $w->time + 28800; // Add 8 Hours
							$data_points .= '[new Date(' . $time . '000), ' . $w->significant_wave_height . ', "' . $label . '", ' . $w->peak_period . ', "' . $label . '"],';
						}
						$max_wave = round($max_wave * 2);
						$max_peak = round($max_peak) + 2; // floor(round($max_peak) / $max_wave) * $max_wave + $max_wave;
					
						$html .= generate_google_chart_with_args(
							array(
								'bouy_id' => $b->buoy_id, 
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
				
				$return = '<div class="panel panel-primary buoy-' . $b->buoy_id . '">' . $html . '</div>';
				print $return;
				
				// Save for Caching
				update_option('datawell_recent_cache_' . $b->buoy_id, '<div class="panel panel-primary buoy-' . $b->buoy_id . ' cached">' . $html . '</div>');
			}
		}
	}
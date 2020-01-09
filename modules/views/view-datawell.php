<?php 
	function uwa_datawell_time_series_csv($buoy_id = '', $from_date = null, $until_date = null) {
		if($buoy_id == '') {
			return;
		}
		
		global $wpdb;

		if($from_date == null) {
			$from_date = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7 * 8)));
		}
		if($until_date == null) {
			$until_date = Date('Y-m-d H:i:s', time());
		}

		$data = $wpdb->get_results(
			$wpdb->prepare( "SELECT id, buoy_id, significant_wave_height AS 'significant_wave_height (meters)', peak_period AS 'peak_period (seconds)', mean_period AS 'mean_period (seconds)', peak_direction AS 'peak_direction (degrees)', peak_directional_spread AS 'peak_directional_spread (degress)', mean_direction AS 'mean_direction (degrees)', mean_directional_spread AS 'mean_directional_spread (degrees)', timestamp, latitude, longitude
				FROM `wp_datawell_post_data_processed_waves` 
				WHERE `buoy_id` = '%s' 
				AND `timestamp` >= '%s' 
				AND `timestamp` <= '%s' 
				ORDER BY `timestamp` ASC",
				$buoy_id,
				$from_date,
				$until_date
			)
		);
		
		uwa_sql_results_to_table($data, $buoy_id);
	}
	
	function uwa_datawell_time_series_data($buoy_id = '', $from_date = null, $until_date = null) {
		
		if($buoy_id == '') {
			return;
		}

		global $wpdb;
		global $uwa_buoy_details;

		if($from_date == null) {
			$from_date = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7)));
		}
		if($until_date == null) {
			$until_date = Date('Y-m-d H:i:s', time());
		}

		$data = $wpdb->get_results(
			$wpdb->prepare( "SELECT * 
				FROM `wp_datawell_post_data_processed_waves` 
				WHERE `buoy_id` = '%s' 
				AND `timestamp` >= '%s' 
				AND `timestamp` <= '%s' 
				ORDER BY `timestamp` ASC",
				$buoy_id,
				$from_date,
				$until_date
			)
		);

		
		if($data) {
			$sig_wave_x = array();
			$sig_wave_y = array();
			
			$max_wave_x = array();
			$max_wave_y = array();

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
				// $timestamp = date('Y-m-d H:i:s', strtotime('-8 hours', strtotime($d->timestamp)));
				$timestamp = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime($d->timestamp)));
				// Significant Wave Height
				if($d->significant_wave_height > 0) {
					array_push($sig_wave_x, $timestamp);
					array_push($sig_wave_y, $d->significant_wave_height);
				}
				
				if($d->max_wave_height > 0) {
					array_push($max_wave_x, $timestamp);
					array_push($max_wave_y, $d->max_wave_height);
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

				// // Peak Direction, Peak Spread(deg)
				if($d->peak_direction > 0) {
					array_push($peak_direction_x, $timestamp);
					array_push($peak_direction_y, $d->peak_direction);
				}
				if($d->peak_directional_spread > 0) {
					array_push($peak_directional_spread_x, $timestamp);
					array_push($peak_directional_spread_y, $d->peak_directional_spread);
				}

				// // Mean Direction, Mean Spread(deg)
				if($d->mean_direction > 0) {
					array_push($mean_direction_x, $timestamp);
					array_push($mean_direction_y, $d->mean_direction);
				}
				if($d->mean_directional_spread > 0) {
					array_push($mean_directional_spread_x, $timestamp);
					array_push($mean_directional_spread_y, $d->mean_directional_spread);
				}
			}
			
			print '<div class="panel panel-primary buoy-' . $data[0]->buoy_id . '">';
				print '<div class="panel-heading">' . $uwa_buoy_details[$data[0]->buoy_id]['title'] . ' Time Series Data (' . date('d-m-Y', strtotime($from_date)) . '  &mdash; ' . date('d-m-Y', strtotime($until_date)) . ')</div>';

				print '<div class="panel-body">';
					generate_time_series_charts($data[0]->buoy_id, $sig_wave_x, $sig_wave_y, $max_wave_x, $max_wave_y, $peak_period_x, $peak_period_y, $mean_period_x, $mean_period_y, $peak_direction_x, $peak_direction_y, $peak_directional_spread_x, $peak_directional_spread_y, $mean_direction_x, $mean_direction_y, $mean_directional_spread_x, $mean_directional_spread_y, true);
				print '</div>';
			print '</div>';
		}
		else {
			print '<div class="panel panel-primary buoy-' . $data[0]->buoy_id . '">';
				print '<div class="panel-body">';
					print 'No data in this date range';
				print '</div>';
			print '</div>';
		}
		
		$from_date_month = date('m', strtotime($from_date)) - 1;
		$until_date_month = date('m', strtotime($until_date)) - 1;

		print '<script type="text/javascript">
			//
		  // Refine Dates on Single Buoy Data
		  //
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
	}
	
	function uwa_datawell_model_results($buoy_id, $from = null, $until = null) {
		global $wpdb, $uwa_photo_lookup, $uwa_buoy_details;	
		
		$title = (isset($uwa_buoy_details[$buoy_id])) ? $uwa_buoy_details[$buoy_id]['title'] : $buoy_id;
		
		$recent = $wpdb->get_row(
			$wpdb->prepare("
				SELECT * FROM `{$wpdb->prefix}datawell_post_data_processed_waves`
	  		WHERE `buoy_id` = '%s'
	  		AND `significant_wave_height` != 0
	  		ORDER BY `timestamp` DESC
	  		LIMIT 1
	  	",
	  	$buoy_id)
	  );
		
		print '<div class="panel panel-primary buoy-' . $buoy_id . '">';
			print '<div class="panel-heading panel-memplot">' . $title .' Wave Buoy</div>';
			print '<div class="panel-body">';
			
				$until_where = '';
				if($until != null) {
					$until_where = " AND timestamp <= '" . $until . "' ";
				}
				
				// Get most recent results
				$memplot = $wpdb->get_row(
					$wpdb->prepare("SELECT * FROM `{$wpdb->prefix}datawell_memplot` 
						WHERE buoy_id='%s' 
						" . $until_where . "
						ORDER BY timestamp 
						DESC LIMIT 1
					", $buoy_id)
				);
				
				if($memplot) {
					$date = date('d-m-Y H:i:s', strtotime($memplot->timestamp));
					print '<div class="col-sm-7">';
						print '<h5 style="text-align: center;" class="panel-memplot">Frequency-Directional Spectrum</h5>';
		

						print '<div class="memplot">
							<img src="/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=image_fetch&key=' . $memplot->url . '" alt="">
							<input type="hidden" name="memplot" value="' . $memplot->url . '">
						</div>';
					print '</div>';
				}
				
				print '<div class="col-sm-5">';
					print '<div>';
					if(isset($uwa_buoy_details[$buoy_id])) {
						if(isset($uwa_buoy_details[$buoy_id]['description'])) {
							print $uwa_buoy_details[$buoy_id]['description'];
						}
						if(!$uwa_buoy_details[$buoy_id]['hide_location']) {
							print '<br>Position: ' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;<br>';	
						}
						if(isset($uwa_buoy_details[$buoy_id]['depth'])) {
							print 'Approx water depth: ' . $uwa_buoy_details[$buoy_id]['depth'];
						}
					}
					print '</div>';
					if(isset($uwa_photo_lookup[$buoy_id])) {
						print '<img src="' . $uwa_photo_lookup[$buoy_id] . '" class="img-thumbnail" style="">';
					}
				print '</div>';
			print '</div>';	
		print '</div>';
		
	}
	
	function uwa_datawell_list_buoys() {
		global $wpdb;
		global $uwa_buoy_details; 

		// Get list of Buoys
		// $buoys = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}datawell_post_data_processed_waves` GROUP BY `buoy_id`");
		$buoys = $wpdb->get_results(" SELECT `buoy_id` FROM `{$wpdb->prefix}buoy_info` WHERE `visible` = 1 AND `buoy_type` = 'datawell'");
		
		foreach($buoys as $b) {
			if($b->buoy_id == 'Offshore') {
				// Skip Offshort Buoy
			}
			else {
			
				print '<div class="panel panel-primary buoy-' . $b->buoy_id . '">';
					// Most recent
					// incase there haven't been a recent post
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
					
					$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
					$last_observation = "";
					if($recent) {
						$date = date('d M, H:i', strtotime($recent->timestamp) + 28800);
						$alert = (strtotime('-120 minutes') > strtotime($recent->timestamp)) ? 'warning' : '';
						$last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . "</span>";
					}
					
					$hide_location = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['hide_location'] : 0;
	
					print '<div class="panel-heading clearfix">
						<h5 style="float: left;">' . $title . ' &mdash; ';
						print (!$hide_location) ? '[' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;] &mdash; ' : '';
						print $last_observation . '</h5>
						<a style="float: right;" href="/datawell?buoy_id=' . $b->buoy_id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>
					</div>';
					print '<div class="panel-body">';
				
						
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
					  
						  generate_google_chart($b->buoy_id, $chart_id, $callback, $data_points, $max_wave, $max_peak, $direction_points, 2);
							
					  	print '<table class="table">';
					  		print '<thead><tr>';
					  			print '<th>Significant Wave Height</th>';
					  			print '<th>Peak Period</th>';
					  			print '<th>Peak Direction</th>';
					  			print '<th>Directional spreading</th>';
					  		print '</tr></thead>';
					  		print '<tbody>';
					  			print '<tr>';
					  				print '<td><strong>' . $recent->significant_wave_height . ' m</strong></td>';
					  				print '<td>' . $recent->peak_period . ' s</td>';
					  				print '<td>' . $recent->peak_direction . ' degrees</td>';
					  				print '<td>' . $recent->peak_directional_spread . ' degrees</td>';
					  			print '</tr>';
					  		print '</tbody>';
					    print '</table>';
					    
					    // print '<a href="/datawell?buoy_id=' . $b->buoy_id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>';
						}
					print '</div>';
				print '</div>';
			}		
		}
	}

	function uwa_datawell_list_markers() {
		global $wpdb;
		global $uwa_buoy_details;

		// Points 
		$points = array();
		
		// Get list of Buoys
		$buoys = $wpdb->get_results(
			"SELECT * FROM 
				(SELECT * FROM wp_datawell_post_data_processed_waves ORDER BY `timestamp` DESC LIMIT 18446744073709551615) 
			AS alias 
			GROUP BY `buoy_id`;");

		foreach($buoys as $b) {
	  	if($b->buoy_id != 'Offshore') {
		  	$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
		  	
	  		$points[] = array(
	  			$b->buoy_id,
	  			$b->id,
	  			$b->latitude,
	  			$b->longitude,
	  			'0',
	  			$title,
	  			get_bloginfo('url') . '/datawell?buoy_id=' . $b->buoy_id
	  		);
	  	}
  	
		}

		print '<script type="text/javascript">';
			print 'var datawell_points = [';
			foreach($points as $k => $p) {
				print '["' . implode('","', $p) . '"],';
			}
			print '];';
		print '</script>';
	}
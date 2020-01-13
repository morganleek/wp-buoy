<?php 
	function uwa_triaxy_list_buoys() {
		global $wpdb;
    global $uwa_buoy_details; 
    
    // Get list of Buoys
    // Set Serial IDs
    $buoys = $wpdb->get_results(
      "SELECT s.id AS buoy_serial_id, b.*
      FROM `{$wpdb->prefix}triaxy_serial_lookup` AS s
      LEFT JOIN `{$wpdb->prefix}buoy_info` AS b
      ON s.buoy_serial = b.buoy_id
      WHERE b.visible = 1 
      AND b.buoy_type = 'triaxy'
    ");
		
		foreach($buoys as $b) {
      $html = '';

      // Most recent
      $recent = $wpdb->get_row(
        $wpdb->prepare("
          SELECT * FROM `{$wpdb->prefix}triaxy_post_data_processed_waves`
          WHERE `buoy_serial_id` = '%s'
          -- AND `significant_wave_height` != 0
          ORDER BY `timestamp` DESC
          LIMIT 1
        ",
        $b->buoy_serial_id)
      );

      // Check for cached chart
      $recent_option = get_option('triaxy_recent_event_' . $b->buoy_serial_id, 0);
      if($recent_option == strtotime($recent->timestamp) && !isset($_GET['flush_charts'])) {
        // Grab Cached Version
        $cached = get_option('triaxy_recent_cache_' . $b->buoy_serial_id, '<p>No cached version available</p>');
        print $cached;
      }
      else {
        // Create new chart
        update_option('triaxy_recent_event_' . $b->buoy_serial_id, strtotime($recent->timestamp));

      
        
        $title = (isset($b->title)) ? $b->title : '';
        $last_observation = "";
        if($recent) {
          $date = date('d M, H:i', strtotime($recent->timestamp) + 28800);
          $alert = (strtotime('-120 minutes') > strtotime($recent->timestamp)) ? 'warning' : '';
          $last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . "</span>";
        }
        
        $hide_location = ($b->hide_location === "1") ? true : false;
        $lat = '';
        $lng = '';
        if(!empty($b->custom_lat) && !empty($b->custom_lng)) {
          $lat = $b->custom_lat;
          $lng = $b->custom_lng;
        }
        else {
          $lat = $recent->latitude;
          $lng = $recent->longitude;
        }

        $html .= '<div class="panel-heading clearfix">
          <h5 style="float: left;">' . $title . ' &mdash; ';
          $html .= (!$hide_location) ? '[' . round($lat, 4) . '&deg;, ' . round($lng, 4) . '&deg;] &mdash; ' : '';
          $html .= $last_observation . '</h5>';
          // $html .= '<a style="float: right;" href="/triaxy?buoy_id=' . $b->buoy_id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>';
        $html .= '</div>';
        $html .= '<div class="panel-body">';
          if($recent) {
            // Get previous 3 days wave height, direction and period data.
            $wave_from = $recent->timestamp;
            $wave_until = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($recent->timestamp))); // 216000sec is 2.5 days

            $waves = $wpdb->get_results(
              $wpdb->prepare("
                SELECT * FROM (
                  SELECT *, UNIX_TIMESTAMP(`timestamp`) AS time
                  FROM `{$wpdb->prefix}triaxy_post_data_processed_waves`
                  WHERE `timestamp` < '%s'
                  AND `timestamp` > '%s'
                  -- AND (`peak_period` != 0 AND `peak_direction` != 0) # disclude empty values
                  ORDER BY `timestamp` DESC
                ) AS S # LIMIT 12",
                $wave_from,
                $wave_until
              )
            );
            
            // $spotter_id = str_replace('-', '_', sanitize_title($b->buoy_serial_id));
            $chart_id = 'triaxy_' . $b->buoy_serial_id . '_chart_div'; // sanitize_title($b->buoy_id) . '_chart_div';
            $callback = 'triaxy_' . $b->buoy_serial_id . 'DrawChart'; // sanitize_title($b->buoy_id) . 'DrawChart';
            
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
                'bouy_id' => $b->buoy_serial_id, 
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
        update_option('triaxy_recent_cache_' . $b->buoy_serial_id, '<div class="panel panel-primary buoy-' . $b->buoy_id . ' cached">' . $html . '</div>');
      }
		}
	}

	// function uwa_triaxy_list_markers() {
	// 	global $wpdb;
	// 	global $uwa_buoy_details;

	// 	// Points 
	// 	$points = array();
		
	// 	// Get list of Buoys
	// 	$buoys = $wpdb->get_results(
	// 		"SELECT * FROM 
	// 			(SELECT * FROM wp_triaxy_post_data_processed_waves ORDER BY `timestamp` DESC LIMIT 18446744073709551615) 
	// 		AS alias 
	// 		GROUP BY `buoy_id`;");

	// 	foreach($buoys as $b) {
	//   	if($b->buoy_id != 'Offshore') {
	// 	  	$title = (isset($uwa_buoy_details[$b->buoy_id])) ? $uwa_buoy_details[$b->buoy_id]['title'] : $b->buoy_id;
		  	
	//   		$points[] = array(
	//   			$b->buoy_id,
	//   			$b->id,
	//   			$b->latitude,
	//   			$b->longitude,
	//   			'0',
	//   			$title,
	//   			get_bloginfo('url') . '/triaxy?buoy_id=' . $b->buoy_id
	//   		);
	//   	}
  	
	// 	}

	// 	print '<script type="text/javascript">';
	// 		print 'var triaxy_points = [';
	// 		foreach($points as $k => $p) {
	// 			print '["' . implode('","', $p) . '"],';
	// 		}
	// 		print '];';
	// 	print '</script>';
	// }
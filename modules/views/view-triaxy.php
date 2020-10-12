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
      AND b.visibility_options != 1
      AND b.buoy_type = 'triaxy'
    ");

    $html_buoys = array();
		
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

      // Get offset
      $uwa_triaxy_time_adjustment = get_option('uwa_triaxy_time_adjustment', '+8') ?: '+8'; // Ternary for empty option values
      
      // Check for cached chart
      $recent_option = get_option('triaxy_recent_event_' . $b->buoy_serial_id, 0);
      if($recent_option == strtotime($recent->timestamp) && !isset($_GET['flush_charts'])) {
        // Grab Cached Version
        $cached = get_option('triaxy_recent_cache_' . $b->buoy_serial_id, '<p>No cached version available</p>');
        $html_buoys[$b->buoy_order . '-' . $b->buoy_id] =  $cached;
      }
      else {
        // Create new chart
        update_option('triaxy_recent_event_' . $b->buoy_serial_id, strtotime($recent->timestamp));
  
        $title = (isset($b->title)) ? $b->title : '';
        $last_observation = "";
        if($recent) { 
          $recent_time = strtotime($recent->timestamp);
          $recent_time_adjusted = strtotime($uwa_triaxy_time_adjustment . ' hours', $recent_time);
          $recent_time_alert = strtotime('-120 minutes', strtotime($uwa_triaxy_time_adjustment)); // strtotime('-120 minutes');
          $date = date('d M, H:i', $recent_time_adjusted);
          $alert = ($recent_time_alert > $recent_time) ? 'warning' : '';
          $last_observation = "Latest Observations at <span class='" . $alert . "'>" . $date . " (" . $uwa_triaxy_time_adjustment . ")</span>";
        }
        
        $hide_location = ($b->hide_location === "1") ? true : false;

        // Grab custom lat/lng if set
				$lat = (!empty($b->custom_lat)) ? $b->custom_lat : $recent->latitude;
        $lng = (!empty($b->custom_lng)) ? $b->custom_lng : $recent->longitude;

        $html .= '<div class="panel-heading clearfix">
          <h5 style="float: left;">' . $title . ' &mdash; ';
          $html .= (!$hide_location) ? '[' . round($lat, 4) . '&deg;, ' . round($lng, 4) . '&deg;] &mdash; ' : '';
          $html .= $last_observation . '</h5>';
          $html .= '<a style="float: right;" href="/triaxy?buoy_id=' . $b->buoy_serial_id . '&buoy_info_id=' . $b->id . '" class="btn btn-success" role="button">Go to ' . $title . ' Data Page</a>';
        $html .= '</div>';
        $html .= '<div class="panel-body">';
          if($recent) {
            // Get previous 3 days wave height, direction and period data.
            $wave_from = $recent->timestamp;
            $wave_until = date('Y-m-d H:i:s', strtotime('-3 days', strtotime($recent->timestamp))); // 216000sec is 2.5 days

            $waves = $wpdb->get_results(
              $wpdb->prepare("
                SELECT * FROM `{$wpdb->prefix}triaxy_post_data_processed_waves`
                WHERE `timestamp` < '%s'
                AND `timestamp` > '%s'
                -- AND (`peak_period` != 0 AND `peak_direction` != 0) # disclude empty values
                AND `buoy_serial_id` = %d
                ORDER BY `timestamp` ASC",
                $wave_from,
                $wave_until,
                $b->buoy_serial_id
              )
            );
            // _d($wpdb);
            // _d($waves);
            
            // $spotter_id = str_replace('-', '_', sanitize_title($b->buoy_serial_id));
            $chart_id = 'triaxy_' . $b->buoy_serial_id . '_chart_div'; // sanitize_title($b->buoy_id) . '_chart_div';
            $callback = 'triaxy_' . $b->buoy_serial_id . 'DrawChart'; // sanitize_title($b->buoy_id) . 'DrawChart';
            
            
            
            $max_wave = 0; 
						$max_peak = 0;
						$data_points = ''; 
            $direction_points = array();
            // True North Offset
            $true_north_offset = (!empty($b->true_north_offset)) ? floatval($b->true_north_offset) : 0;
            foreach($waves as $k => $w) {
              $direction_points[] = ((floor(($w->peak_direction + $true_north_offset) / 10) * 10) + 180) % 360; // Flip direction 
              $max_wave = ($w->significant_wave_height > $max_wave) ? $w->significant_wave_height : $max_wave;
              $max_peak = ($w->peak_period > $max_peak) ? $w->peak_period : $max_peak;
              // Adjust time from GMT using offset
              $wave_time = strtotime($w->timestamp); // GMT
              $adjusted_time = strtotime($uwa_triaxy_time_adjustment . ' hours', $wave_time);
              $label = 'Location: ' . date('M d, Y g:iA', $adjusted_time) . ' (' . $uwa_triaxy_time_adjustment . ')\nGMT: ' . date('M d, Y g:iA', $wave_time) . '\nSignificant Wave Height: ' . $w->significant_wave_height . ' m\nPeak Period: ' . $w->peak_period . ' s';
              $data_points .= '[new Date(' . $wave_time . '000), ' . $w->significant_wave_height . ', "' . $label . '", ' . $w->peak_period . ', "' . $label . '"],';
            }
            
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
            
            $max_wave = round($max_wave * 2);
            $max_peak = round($max_peak) + 2; // floor(round($max_peak) / $max_wave) * $max_wave + $max_wave;
            
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
        update_option('triaxy_recent_cache_' . $b->buoy_serial_id, '<div class="panel panel-primary buoy-' . $b->buoy_id . ' cached">' . $html . '</div>');
      }
    }
    
    return $html_buoys;
	}

	
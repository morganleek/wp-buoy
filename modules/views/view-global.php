<?php
	// Home Views List
	function uwa_list_buoys() {
		// Get Datawell 
		$datawell = uwa_datawell_list_buoys();
		// Get Spoondrift
		$spoondrift = uwa_spoondrift_list_buoys();
		// Get Triaxy
		$triaxy = uwa_triaxy_list_buoys();
		
		// Sort by Key
		$buoys = array_merge($datawell, $spoondrift, $triaxy);
		ksort($buoys, SORT_NUMERIC);
		
		foreach($buoys as $b) {
			print $b;
		}
	}

	function uwa_global_header_scripts() {
		if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
			// jQuery
			wp_deregister_script('jquery');
			wp_register_script('jquery', get_template_directory_uri() . '/bower_components/jquery/dist/jquery.min.js', array(), '2.2.3');
			
			// Date Ranage Picker
			wp_register_script('moment', "https://cdn.jsdelivr.net/momentjs/latest/moment.min.js", array(), '1.0.1593588250014');
			wp_register_script('date-range-picker', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js", array('moment'), '1.0.1593588250014');
	
			// Google Charts
			// wp_register_script('google-charts', 'https://www.gstatic.com/charts/loader.js', array(), '1.0.1593588250014');
	
			// Theme Scripts
			wp_register_script('scm_scripts', UWA__PLUGIN_URL . 'modules/views/js/theme.js', array('jquery', 'date-range-picker', 'jquery-ui-core', 'jquery-ui-progressbar', 'jquery-ui-dialog'), '1.1.20');
	
			// Enqueue Scripts
			wp_enqueue_script('scm_scripts');
			
			// Starting Lat, Lng
			$lat = get_option('uwa_map_starting_position_lat');
			$lng = get_option('uwa_map_starting_position_lng');
			if(!$lat || !$lng) {
				// Default to WA
				$lat = '-33.0973889';
				$lng = '116.9485048';
			}
			
			// Add AJAX Object
			$api = get_option('uwa_google_maps_api');

			wp_localize_script('scm_scripts', 
				'ajax_object', 
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'plugin_url' => UWA__PLUGIN_URL,
					'starting_lat' => $lat,
					'starting_lng' => $lng,
					'google_maps_api_key' => $api
				)
			);
			
			// Maps Footer Scripts
			// if( $api ) {
			// 	wp_register_script('google-maps', 'https://maps.googleapis.com/maps/api/js?v=3&key=' . $api, array(), '1.0.2', true); // &callback=initMap
			// 	wp_register_script('google-maps-marker-with-label', UWA__PLUGIN_URL . 'modules/views/js/v3-utility-library/markerwithlabel/src/markerwithlabel.js', array('google-maps'), '1.2.4', true);
			// 	wp_register_script('google-maps-init', UWA__PLUGIN_URL . 'modules/views/js/google-maps-init.js', array('google-maps', 'google-maps-marker-with-label'), '1.0.20', true);
			// 	wp_enqueue_script('google-maps-init');
			// }

			// Replacement bundled scripts
			wp_register_script('scm_scripts_bundled', UWA__PLUGIN_URL . 'dist/js/bundle.js', array(), '1.0.0');
			wp_enqueue_script('scm_scripts_bundled');

			// Bundled style
			wp_register_style( 'scm_styles_bundled',  UWA__PLUGIN_URL . 'dist/css/bundle.css', array(), '1.0.0', 'all' );
			wp_enqueue_style( 'scm_styles_bundled' );
		}
		if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin() && !is_page('home')) {
			// Conditional script(s)
			wp_register_script('plotly', 'https://cdn.plot.ly/plotly-latest.min.js', array(), '1.0.0');
			wp_enqueue_script('plotly');
		}
	}

	add_action('init', 'uwa_global_header_scripts'); // Add Custom Scripts to wp_head

	function uwa_global_list_markers() {
		if(is_page('home')) {
			global $wpdb;
			global $uwa_buoy_details; 

			// Points 
			$points = array();
			
			// Get list of Buoys
			$buoys = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}buoy_info` WHERE `visible` = 1 AND `visibility_options` != 2");

			foreach($buoys as $b) {
				// Points 
				$point = array(
					'buoy_id' => $b->buoy_id,
					'id' => 0,
					'lat' => 0,
					'lng' => 0,
					'mean_direction' => 0,
					'title' => (!empty($b->title)) ? $b->title : $b->buoy_id,
					'url' => '',
					// 'type' => ''
				);

				switch($b->buoy_type) {
					case 'spoondrift':
						$r = $wpdb->get_row("
							SELECT * FROM 
							(SELECT * FROM `{$wpdb->prefix}spoondrift_post_data_processed` WHERE `spotter_id` = '" . $b->buoy_id . "' ORDER BY id DESC LIMIT 1) AS P
							LEFT JOIN `{$wpdb->prefix}spoondrift_post_data_processed_waves` AS W
							ON P.`id` = W.`post_data_processed_id`
							ORDER BY W.`timestamp` DESC
							LIMIT 1
						");
						
						$point['id'] = $r->id;
						$point['lat'] = $r->latitude;
						$point['lng'] = $r->longitude;
						$point['mean_direction'] = $r->mean_direction;
						$point['url'] = get_bloginfo('url') . '/spoondrift?spotter_id=' . $b->buoy_id . '&buoy_info_id=' . $b->id;
						// $point ['type'] = 'spoondrift';

						break;
					case 'datawell':
						$r = $wpdb->get_row(
							$wpdb->prepare("
								SELECT * FROM {$wpdb->prefix}datawell_post_data_processed_waves 
								WHERE `buoy_id` = '%s' 
								ORDER BY `timestamp` DESC LIMIT 1
							",
							$b->buoy_id)
						);
						if($wpdb->num_rows > 0) {
							$point['id'] = $r->id;
							$point['lat'] = $r->latitude;
							$point['lng'] = $r->longitude;
							$point['mean_direction'] = 0;
							$point['url'] = get_bloginfo('url') . '/datawell?buoy_id=' . $b->buoy_id . '&buoy_info_id=' . $b->id;
						}

						// $point ['type'] = 'datawell';
				
						break;
					case 'triaxy':
						// Lookup Serial ID
						$s = $wpdb->get_row(
							$wpdb->prepare("
								SELECT * FROM `wp_triaxy_serial_lookup`
								WHERE `buoy_serial` = '%s'
								LIMIT 1
							", 
							$b->buoy_id
							)
						);

						if($wpdb->num_rows > 0) {
							// Get most recent processed wave
							$r = $wpdb->get_row(
								$wpdb->prepare("
									SELECT * FROM {$wpdb->prefix}triaxy_post_data_processed_waves 
									WHERE `id` = '%s' 
									ORDER BY `timestamp` DESC LIMIT 1
								",
								$s->id)
							);
							
							if($wpdb->num_rows > 0) {
								$point['id'] = $s->id;
								$point['lat'] = (!empty($r->latitude)) ? $r->latitude : 0;
								$point['lng'] = (!empty($r->longitude)) ? $r->longitude : 0;
								$point['mean_direction'] = 0;
								$point['url'] = get_bloginfo('url') . '/triaxy?buoy_id=' . $s->id . '&buoy_info_id=' . $b->id;
							}

							// $point ['type'] = 'triaxy';
						}
						break;
					default:
						break;
				}
				
				// Use custom
				if(!empty($b->custom_lat) && !empty($b->custom_lng)) {
					$point['lat'] = $b->custom_lat;
					$point['lng'] = $b->custom_lng;
				}

				$points[] = $point;
			}

			print '<script type="text/javascript">';
				print 'var global_points = [';
				foreach($points as $k => $p) {
					print '["' . implode('","', $p) . '"],';
				}
				print '];';
				
				print 'var global_points_object = ' . json_encode($points) . ';';
			print '</script>';
		}
	}

	add_action('wp_print_scripts', 'uwa_global_list_markers');

	function uwa_global_template_search($args = array()) {
		$defaults = array(
			'buoy_id' => 0,
			'buoy_info_id' => 0,
			'buoy_type' => '',
			'dates' => true,
			'csv_form' => false,
			'csv_form_2d' => false,
			'csv_form_1d' => false,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);

		if($_args['buoy_id'] === 0 || $_args['buoy_info_id'] === 0 || $_args['buoy_type'] === '') {
			return;
		}

		$html = '';
		
		$html .= '<div class="refine-dates">';
			if($_args['dates']) {
				$html .= '
					<form action="" method="post">
						<input type="hidden" name="spotter_id" value="' . $_args['buoy_id'] . '">
						<label for="from">Date</label>
						<input type="text" name="dates" autocomplete="off" value="">
						<button class="btn btn-default">Refine Time Period</button>
					</form>
				';
			}
			if($_args['csv_form']) {
				$html .= '
					<form class="csv-form" action="" method="post">
						<input type="hidden" name="csv-buoy_id" value="' . $_args['buoy_id'] . '">
						<input type="hidden" name="csv-type" value="' . $_args['buoy_type'] . '">
						<input type="hidden" name="csv-dates" value="">
						<input type="hidden" name="csv" value="csv">
						<button class="btn btn-info">Download Time Series Data</button>
					</form>
				';
			}
			if($_args['csv_form_2d']) {
				$html .= '
					<form class="csv-form csv-form-2d" action="" method="post">
						<input type="hidden" name="csv-buoy_id" value="' . $_args['buoy_id'] . '">
						<input type="hidden" name="csv-type" value="' . $_args['buoy_type'] . '">
						<input type="hidden" name="csv-dates" value="">
						<input type="hidden" name="spectrum" value="2d">
						<input type="hidden" name="send-memplot" value="">
						<button class="btn btn-success">Download 2D spectrum</button>
					</form>
				';
			}
			if($_args['csv_form_1d']) {
				$html .= '
					<form class="csv-form csv-form-1d" action="" method="post">
						<input type="hidden" name="csv-buoy_id" value="' . $_args['buoy_id'] . '">
						<input type="hidden" name="csv-type" value="' . $_args['buoy_type'] . '">
						<input type="hidden" name="csv-dates" value="">
						<input type="hidden" name="spectrum" value="1d">
						<input type="hidden" name="send-memplot" value="">
						<button class="btn btn-warning">Download 1D spectrum</button>
					</form>
				';
			}
		$html .= '</div>';

		if($_args['return']) {
			return $html;
		}
		print $html;
	}

	function uwa_global_template_date_range($post_date = '') {
		$return = array(
			'has_date' => false,
			'from' => '', 
			'until' => ''
		);

		if(!empty($post_date)) {
			$dates = explode(' - ', $post_date);
			
			if(sizeof($dates) === 2) {
				$from_exploded = explode('/', $dates[0]);
				$return['from'] = date('Y-m-d 00:00:00', strtotime($from_exploded[1] . '/' . $from_exploded[0] . '/' . $from_exploded[2]));
				$until_exploded = explode('/', $dates[1]);
				$return['until'] = date('Y-m-d 23:59:59', strtotime($until_exploded[1] . '/' . $until_exploded[0] . '/' . $until_exploded[2]));
				$return['has_date'] = true;
			}
		}

		return $return;
	}

	function uwa_global_template_general($args = array()) {
		$html = '';

		$defaults = array(
			'buoy_id' => '',
			'buoy_info_id' => '',
			'buoy_type' => '',
			'dates' => true,
			'csv_form' => false,
			'csv_form_2d' => false,
			'csv_form_1d' => false,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);

		if(!empty($_args['buoy_id']) && !empty($_args['buoy_type'])) {
			// Search form
			$html .= uwa_global_template_search(array(
				'buoy_id' => $_args['buoy_id'],
				'buoy_info_id' => $_args['buoy_info_id'],
				'buoy_type' => $_args['buoy_type'],
				'dates' => $_args['dates'],
				'csv_form' => $_args['csv_form'],
				'csv_form_2d' => $_args['csv_form_2d'],
				'csv_form_1d' => $_args['csv_form_1d'],
				'return' => true
			));

			// Date range extracted
			$date_range = uwa_global_template_date_range($_POST['dates']);

			// Layout Time Series & Model Results
			$search_params = array(
				'buoy_id' => $_args['buoy_id'], 
				'buoy_info_id' => $_args['buoy_info_id'], 
				'buoy_type' => $_args['buoy_type'],
				'return' => true
			);
			// Add date
			if($date_range['has_date']) {
				$search_params['from_date'] = $date_range['from'];
				$search_params['until_date'] = $date_range['until'];
			}	

			$html .= '<div class="single-buoy-data">';
				$html .= '<div>';
					$html .= call_user_func('uwa_global_time_series_data_with_args', $search_params);
				$html .= '</div>';
				$html .= '<div>';
					$message = get_option('uwa_copyright_message', '<p>Click to continue</p>');
					$html .= call_user_func('uwa_global_model_results_with_args', $search_params);
					$html .= uwa_terms_popup($message, true);
				$html .= '</div>';
			$html .= '</div>';
		}
		else {
			$html .= '<p>No Spotter ID Specified.</p>';
		}

		if($_args['return']) {
			return $html;
		}
		print $html;
	}

	function uwa_global_time_series_data_with_args($args) {
		$html = '';
				
		$defaults = array(
			'buoy_id' => '', 
			'buoy_info_id' => '',
			'buoy_type' => '',
			'from_date' => null, 
			'until_date' => null,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);
		
		if($_args['buoy_id'] == '' || $_args['buoy_type'] == '' || $_args['buoy_info_id'] === '') {
			return $html;
		}
		
		$from_date = $_args['from_date'];
		$until_date = $_args['until_date'];
		
		global $wpdb;
		global $uwa_buoy_details;

		$buoy_info = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}buoy_info
				WHERE `id` = '%d'",
				$_args['buoy_info_id']
			)
		);
		$true_north_offset = $buoy_info->true_north_offset;

		if($from_date == null) {
			$from_date = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7 * 8)));
		}
		if($until_date == null) {
			$until_date = Date('Y-m-d H:i:s', time());
		}

		$data = null;

		switch($_args['buoy_type']) {
			case 'spoondrift':
				$data = $wpdb->get_results(
					$wpdb->prepare( "SELECT * 
						FROM `wp_spoondrift_post_data_processed` AS P 
						LEFT JOIN `wp_spoondrift_post_data_processed_waves` AS W 
						ON P.`id` = W.`post_data_processed_id` 
						WHERE P.`spotter_id` = '%s' 
						AND W.`timestamp` >= '%s'
						AND W.`timestamp` <= '%s'
						ORDER BY W.`timestamp` ASC",
						$_args['buoy_id'],
						$from_date,
						$until_date
					)
				);
				break;
			case 'datawell':
				$data = $wpdb->get_results(
					$wpdb->prepare( "SELECT * 
						FROM `wp_datawell_post_data_processed_waves` 
						WHERE `buoy_id` = '%s' 
						AND `timestamp` >= '%s' 
						AND `timestamp` <= '%s' 
						ORDER BY `timestamp` ASC",
						$_args['buoy_id'],
						$from_date,
						$until_date
					)
				);
				break;
			case 'triaxy':
				$data = $wpdb->get_results(
					$wpdb->prepare( "SELECT * 
						FROM `wp_triaxy_post_data_processed_waves` 
						WHERE `buoy_serial_id` = '%s' 
						AND `timestamp` >= '%s' 
						AND `timestamp` <= '%s' 
						ORDER BY `timestamp` ASC",
						$_args['buoy_id'],
						$from_date,
						$until_date
					)
				);
				break;
			default:
				_d('Buoy type: ' . $_args['buoy_type'] . ', ' . $_args['buoy_id'] . ' has no data SQL in uwa_global_time_series_data_with_args()');
				die();
				break;
		}

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
				if(!$offset = get_option('uwa_' . $_args['buoy_type'] . '_time_adjustment')) {
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
					$peak_direction = (floatval($d->peak_direction) + floatval($true_north_offset)) % 360;
					array_push($peak_direction_x, $timestamp);
					array_push($peak_direction_y, $peak_direction);
				}
				
				if($d->peak_directional_spread > 0) {
					array_push($peak_directional_spread_x, $timestamp);
					array_push($peak_directional_spread_y, $d->peak_directional_spread);
				}
				
				// // Mean Direction, Mean Spr
				if($d->mean_direction > 0) {
					$mean_direction = (floatval($d->mean_direction) + floatval($true_north_offset)) % 360;
					array_push($mean_direction_x, $timestamp);
					array_push($mean_direction_y, $mean_direction);
				}
				
				if($d->mean_directional_spread > 0) {
					array_push($mean_directional_spread_x, $timestamp);
					array_push($mean_directional_spread_y, $d->mean_directional_spread);
				}

			}

			$title = (isset($uwa_buoy_details[$data[0]->spotter_id])) ? $uwa_buoy_details[$data[0]->spotter_id]['title'] : $data[0]->spotter_id;

			$html .= '<div class="panel panel-primary">';
				$html .= '<div class="panel-heading">' . $title . ' (' . ucfirst($_args['buoy_type']) . ') &mdash; Time Series Data<br>( ' . date('d-m-Y', strtotime($from_date)) . '  &mdash; ' . date('d-m-Y', strtotime($until_date)) . ' )</div>';

				$html .= '<div class="panel-body">';
					$html .= generate_time_series_charts($_args['buoy_id'], $sig_wave_x, $sig_wave_y, array(), array(), $peak_period_x, $peak_period_y, $mean_period_x, $mean_period_y, $peak_direction_x, $peak_direction_y, $peak_directional_spread_x, $peak_directional_spread_y, $mean_direction_x, $mean_direction_y, $mean_directional_spread_x, $mean_directional_spread_y, false, true);
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

	function uwa_global_model_results_with_args($args) {
		$defaults = array(
			'buoy_id' => '',
			'buoy_info_id' => '',
			'buoy_type' => '', 
			'from_date' => null, 
			'until_date' => null,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);

		$buoy_id = $_args['buoy_id'];
		$buoy_info_id = $_args['buoy_info_id'];
		$buoy_type = $_args['buoy_type'];
		$from_date = $_args['from_date'];
		$until_date = $_args['until_date'];

		$html = '';

		if($buoy_id == '' || $buoy_type == '' || $buoy_info_id == '') {
			return $html;
		}

		global $wpdb, $uwa_photo_lookup, $uwa_buoy_details;	

		$title = uwa_global_get_buoy_title($buoy_id, $buoy_type);
		
		switch($buoy_type) {
			case 'spoondrift':
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
				$recent = $wpdb->get_row(
					$wpdb->prepare("
						SELECT * FROM (
							SELECT * FROM 
							`{$wpdb->prefix}spoondrift_post_data_processed` 
							WHERE `spotter_id` = '%s' 
							ORDER BY id 
							DESC LIMIT 1
						) AS P
						LEFT JOIN `{$wpdb->prefix}spoondrift_post_data_processed_waves` AS W
						ON P.`id` = W.`post_data_processed_id`
						ORDER BY W.`timestamp` DESC
						LIMIT 1
					",
					$buoy_id)
				);	
				break;
			case 'datawell':
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
				break;
			case 'triaxy':
				$recent = $wpdb->get_row(
					$wpdb->prepare("
						SELECT * FROM `{$wpdb->prefix}triaxy_post_data_processed_waves`
						WHERE `buoy_serial_id` = '%s'
						AND `significant_wave_height` != 0
						ORDER BY `timestamp` DESC
						LIMIT 1
					",
					$buoy_id)
				);
				break;
				
			default: 
				break;
		}
		
		$html .= '<div class="panel panel-primary">';
			$html .= '<div class="panel-heading panel-memplot">' . $title .' Wave Buoy</div>';
			$html .= '<div class="panel-body">';
			
				$until_where = '';
				if($until != null) {
					$until_where = " AND timestamp <= '" . $until . "' ";
				}
				
				switch($buoy_type) {
					case 'spoondrift':
					case 'datawell':
						$memplot = $wpdb->get_row(
							$wpdb->prepare("SELECT * FROM `{$wpdb->prefix}{$buoy_type}_memplot` 
								WHERE buoy_id='%s' 
								" . $until_where . "
								ORDER BY timestamp 
								DESC LIMIT 1
							", $buoy_id)
						);
						// [id] => 1004
						// [buoy_id] => SPOT-0093
						// [timestamp] => 2020-01-09 01:46:00
						// [url] => SpoondriftBuoys/PerthCanyon/MEMplot/2020/01/PerthCanyon_MEMplot_20200109_0146UTC.jpg
						break;
					default: 
						break;
				}
				
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
					$buoy_id_details = $buoy_id;
					if($buoy_type === 'triaxy') {
						// Look up buoy id slug
						$buoy_id_details = $wpdb->get_var(
							$wpdb->prepare(
								"SELECT buoy_serial 
								FROM {$wpdb->prefix}triaxy_serial_lookup
								WHERE id = %d",
								$buoy_id
							)
						);
					}
					if(isset($uwa_buoy_details[$buoy_id_details])) {
						if(isset($uwa_buoy_details[$buoy_id_details]['description'])) {
							$html .= $uwa_buoy_details[$buoy_id_details]['description'];
						}
						
						if(!$uwa_buoy_details[$buoy_id_details]['hide_location']) {
							if(isset($uwa_buoy_details[$buoy_id_details]['custom_lat']) && isset($uwa_buoy_details[$buoy_id_details]['custom_lat']) && $uwa_buoy_details[$buoy_id_details]['custom_lat'] != 0) {
								$html .= '<br>Position <br>(' . round($uwa_buoy_details[$buoy_id_details]['custom_lat'], 4) . '&deg; ' . round($uwa_buoy_details[$buoy_id_details]['custom_lng'], 4) . '&deg;)';
							}
							else {
								$html .= '<br>Position <br>(' . round($recent->latitude, 4) . '&deg;, ' . round($recent->longitude, 4) . '&deg;)';	
							}
						}
						if(isset($uwa_buoy_details[$buoy_id_details]['depth']) && !empty($uwa_buoy_details[$buoy_id_details]['depth'])) {
							$html .= '<br>Approx water depth: ' . $uwa_buoy_details[$buoy_id_details]['depth'];
						}
					}
					$html .= '</div>';
					if(isset($uwa_photo_lookup[$buoy_id_details]) && !empty($uwa_photo_lookup[$buoy_id_details])) {
						$html .= '<img src="' . $uwa_photo_lookup[$buoy_id_details] . '" class="img-thumbnail" style="">';
					}
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		if($_args['return']) {
			return $html;
		}
		print $html;
	}

	function uwa_global_get_buoy_title($buoy_id = '', $buoy_type = '') {
		global $wpdb, $uwa_buoy_details;

		if($buoy_type === 'triaxy') {
			// Look up buoy id slug
			$buoy_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT buoy_serial 
					FROM {$wpdb->prefix}triaxy_serial_lookup
					WHERE id = %d",
					$buoy_id
				)
			);
		}

		return (isset($uwa_buoy_details[$buoy_id])) ? $uwa_buoy_details[$buoy_id]['title'] : $buoy_id;

	}

	function uwa_global_time_series_csv($args) {
		global $wpdb;

		$defaults = array(
			'buoy_id' => '',
			'buoy_type' => '', 
			'from_date' => null, 
			'until_date' => null
		);

		$_args = wp_parse_args($args, $defaults);

		if($_args['buoy_id'] == '' || $_args['buoy_type'] == '') {
			return;
		}
		

		if($_args['from_date'] == null) {
			$_args['from_date'] = Date('Y-m-d H:i:s', (time() - (60 * 60 * 24 * 7)));
		}
		if($_args['until_date'] == null) {
			$_args['until_date'] = Date('Y-m-d H:i:s', time());
		}

		switch($_args['buoy_type']) {
			case 'spoondrift':
				$data = $wpdb->get_results(
					$wpdb->prepare( "SELECT * 
						FROM `{$wpdb->prefix}spoondrift_post_data_processed` AS P 
						LEFT JOIN `{$wpdb->prefix}spoondrift_post_data_processed_waves` AS W 
						ON P.`id` = W.`post_data_processed_id` 
						WHERE P.`spotter_id` = '%s' 
						AND W.`timestamp` >= '%s'
						AND W.`timestamp` <= '%s'
						ORDER BY W.`timestamp` ASC",
						$_args['buoy_id'],
						$_args['from_date'],
						$_args['until_date']
					)
				);
				break;
			case 'datawell':
				$data = $wpdb->get_results(
					$wpdb->prepare( "SELECT id, buoy_id, significant_wave_height AS 'significant_wave_height (meters)', peak_period AS 'peak_period (seconds)', mean_period AS 'mean_period (seconds)', peak_direction AS 'peak_direction (degrees)', peak_directional_spread AS 'peak_directional_spread (degress)', mean_direction AS 'mean_direction (degrees)', mean_directional_spread AS 'mean_directional_spread (degrees)', timestamp, latitude, longitude
						FROM `{$wpdb->prefix}datawell_post_data_processed_waves` 
						WHERE `buoy_id` = '%s' 
						AND `timestamp` >= '%s' 
						AND `timestamp` <= '%s' 
						ORDER BY `timestamp` ASC",
						$_args['buoy_id'],
						$_args['from_date'],
						$_args['until_date']
					)
				);
				break;
			case 'triaxy':
				$data = $wpdb->get_results(
					$wpdb->prepare( "SELECT id, buoy_serial_id, significant_wave_height AS 'significant_wave_height (meters)', peak_period AS 'peak_period (seconds)', mean_period AS 'mean_period (seconds)', peak_direction AS 'peak_direction (degrees)', peak_directional_spread AS 'peak_directional_spread (degress)', mean_direction AS 'mean_direction (degrees)', mean_directional_spread AS 'mean_directional_spread (degrees)', timestamp, latitude, longitude
						FROM `{$wpdb->prefix}triaxy_post_data_processed_waves`  
						WHERE `buoy_serial_id` = '%s' 
						AND `timestamp` >= '%s' 
						AND `timestamp` <= '%s' 
						ORDER BY `timestamp` ASC",
						$_args['buoy_id'],
						$_args['from_date'],
						$_args['until_date']
					)
				);
				break;
			default:
				_d('No CSV SQL for this buoy type');
				break;
		}

		uwa_sql_results_to_table($data, $_args['buoy_id']);
	}

	function uwa_global_get_label($class = '', $label = '') {
		if(empty($class)) {
			return $label;
		}
		
		$overrides = json_decode(get_option('uwa_label_overrides', '{}'));
		if(isset($overrides->$class) && !empty($overrides->$class)) {
			return $overrides->$class;
		}
		return $label;
	}

	function uwa_wave_points_json( ) {
		global $wpdb;

		if( isset( $_REQUEST ) ) {
			if( isset( $_REQUEST['buoy_type'] ) &&
					isset( $_REQUEST['buoy_id'] ) &&
					isset( $_REQUEST['wave_from'] ) &&
					isset( $_REQUEST['wave_until'] ) &&
					isset( $_REQUEST['time_adjustment'] ) ) {

				$buoy_type = sanitize_text_field( $_REQUEST['buoy_type'] );						
				$buoy_id = sanitize_text_field( $_REQUEST['buoy_id'] );
				$wave_from = sanitize_text_field( html_entity_decode($_REQUEST['wave_from']) );
				$wave_until = sanitize_text_field( html_entity_decode($_REQUEST['wave_until']) );
				$time_adjustment = sanitize_text_field( $_REQUEST['time_adjustment'] );

				$query = "";
						
				switch( $buoy_type ) {
					case 'datawell': 
						$query = '
						SELECT *, UNIX_TIMESTAMP(`timestamp`) AS time
						FROM `' . $wpdb->prefix . 'datawell_post_data_processed_waves`
						WHERE `timestamp` < "%1$s"
						AND `timestamp` > "%2$s"
						AND (`peak_period` != 0 AND `peak_direction` != 0) # disclude empty values
						AND `buoy_id` = "%3$s"
						ORDER BY `timestamp` ASC';
						break;
					case 'spoondrift':
						$query = '
						SELECT * FROM 
						(SELECT * FROM `' . $wpdb->prefix . 'spoondrift_post_data_processed` WHERE spotter_id = "%3$s") AS P
						INNER JOIN
						(SELECT * FROM `' . $wpdb->prefix . 'spoondrift_post_data_processed_waves` WHERE `timestamp` < "%1$s" AND `timestamp` > "%2$s") AS W
						ON P.id = W.post_data_processed_id
						ORDER BY W.`timestamp`';
						break;
					default;
						break;
				}
				

				if( !empty( $query ) ) {
					$waves = $wpdb->get_results(
						$wpdb->prepare(
							$query,
							$wave_from,
							$wave_until,
							$buoy_id
						)
					);

					print_r(json_encode($waves));
					wp_die();
				}
				wp_die();
			}
		}

		wp_die();
		// $buoy, $wave_from, $wave_until, $time_adjustment
	}

	add_action( 'wp_ajax_uwa_wave_points_json', 'uwa_wave_points_json' );
	add_action( 'wp_ajax_nopriv_uwa_wave_points_json', 'uwa_wave_points_json' );
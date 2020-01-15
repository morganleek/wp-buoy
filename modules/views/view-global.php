<?php
	// Home Views List
	function uwa_list_buoys() {
		uwa_datawell_list_buoys();
		uwa_spoondrift_list_buoys();
		uwa_triaxy_list_buoys();
	}

	function uwa_global_header_scripts() {
		if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
			// jQuery
			wp_deregister_script('jquery');
			wp_register_script('jquery', get_template_directory_uri() . '/bower_components/jquery/dist/jquery.min.js', array(), '2.2.3');
			
			// Date Ranage Picker
			wp_register_script('moment', "https://cdn.jsdelivr.net/momentjs/latest/moment.min.js", array(), '1.0.0');
			wp_register_script('date-range-picker', "https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js", array('moment'), '1.0.0');
	
			// Google Charts
			wp_register_script('google-charts', 'https://www.gstatic.com/charts/loader.js', array(), '1.0.0');
	
			// Theme Scripts
			wp_register_script('scm_scripts', UWA__PLUGIN_URL . 'modules/views/js/theme-min.js', array('jquery', 'date-range-picker', 'google-charts', 'jquery-ui-core', 'jquery-ui-progressbar', 'jquery-ui-dialog'), '1.1.12');
	
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
			wp_localize_script('scm_scripts', 
				'ajax_object', 
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'starting_lat' => $lat,
					'starting_lng' => $lng
				)
			);
			
			// Maps Footer Scripts
			if($api = get_option('uwa_google_maps_api')) {
				wp_register_script('google-maps', 'https://maps.googleapis.com/maps/api/js?v=3&key=' . $api, array(), '1.0.2', true); // &callback=initMap
				wp_register_script('google-maps-marker-with-label', UWA__PLUGIN_URL . 'modules/views/js/v3-utility-library/markerwithlabel/src/markerwithlabel.js', array('google-maps'), '1.2.4', true);
				wp_register_script('google-maps-init', UWA__PLUGIN_URL . 'modules/views/js/google-maps-init.js', array('google-maps', 'google-maps-marker-with-label'), '1.0.4', true);
				wp_enqueue_script('google-maps-init');
			}
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
			$buoys = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}buoy_info` WHERE `visible` = 1");

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
						$point['url'] = get_bloginfo('url') . '/spoondrift?spotter_id=' . $b->buoy_id;
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
							$point['url'] = get_bloginfo('url') . '/datawell?buoy_id=' . $b->buoy_id;
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
								$point['url'] = get_bloginfo('url') . '/triaxy?buoy_id=' . $b->buoy_id;
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
			print '</script>';
		}
	}

	add_action('wp_print_scripts', 'uwa_global_list_markers');
<?php
	// Home Views List
	function uwa_list_buoys() {
		uwa_datawell_list_buoys();
		uwa_spoondrift_list_buoys();
		// uwa_triaxy_list_buoys();
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

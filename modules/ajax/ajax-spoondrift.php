<?php
	/* 
	 ** AJAX
	 ** Called via CRON
 	*/ 

	// Specrum 1D from AWS
	function update_spoondrift_1d() {
		uwa_log('spoondrift', 'Grad and Fill: "SpoondriftBuoys", "Spec1D", "spoondrift", "spec_1d", "_Spec1D_"');
		uwa_grab_and_fill('SpoondriftBuoys', 'Spec1D', 'spoondrift', 'spec_1d', '_Spec1D_');
		
		print 1;
		wp_die();
	}

	add_action( 'wp_ajax_update_spoondrift_1d', 'update_spoondrift_1d' );
	add_action( 'wp_ajax_nopriv_update_spoondrift_1d', 'update_spoondrift_1d' );

	// Specrum 2D from AWS
	function update_spoondrift_2d() {
		uwa_log('spoondrift', 'Grad and Fill: "SpoondriftBuoys", "Spec2D", "spoondrift", "spec_2d", "_Spec2D_"');
		uwa_grab_and_fill('SpoondriftBuoys', 'Spec2D', 'spoondrift', 'spec_2d', '_Spec2D_');
		
		print 1;
		wp_die();
	}

	add_action( 'wp_ajax_update_spoondrift_2d', 'update_spoondrift_2d' );
	add_action( 'wp_ajax_nopriv_update_spoondrift_2d', 'update_spoondrift_2d' );

	// Specrum Memplots from AWS
	function update_spoondrift_memplots() {
		uwa_log('spoondrift', 'Grad and Fill: "SpoondriftBuoys", "MEMplot", "spoondrift", "memplot", "_MEMplot_"');
		uwa_grab_and_fill('SpoondriftBuoys', 'MEMplot', 'spoondrift', 'memplot', '_MEMplot_');
		
		print 1;
		wp_die();
	}

	add_action( 'wp_ajax_update_spoondrift_memplots', 'update_spoondrift_memplots' );
	add_action( 'wp_ajax_nopriv_update_spoondrift_memplots', 'update_spoondrift_memplots' );

	// Receive JSON Data
	function uwa_receive_spoondrift_data() {
		global $wpdb;
	
		$content = '';

		// Received Data
		$file_contents = file_get_contents('php://input');
		$data = json_decode($file_contents, true);

		// Debug Dump
		$uwa_spoondrift_post_logging = get_option( 'uwa_spoondrift_post_logging' );
		if($uwa_spoondrift_post_logging === "1") {
			file_put_contents(UWA__PLUGIN_DIR . 'modules/ajax/raw/spotter-' . time() . '.json', $file_contents);
		}

		uwa_receive_spoondrift_process_json($data, $file_contents);
		
		wp_die();
	}
	
	add_action( 'wp_ajax_uwa_receive_spoondrift_data', 'uwa_receive_spoondrift_data' );
	add_action( 'wp_ajax_nopriv_uwa_receive_spoondrift_data', 'uwa_receive_spoondrift_data' );

	function uwa_receive_spoondrift_process_json($data = null, $raw = null) {
		global $wpdb;

		if($data != null && $raw != null) {
			// Check for SpotterID
			if(isset($data['data']['spotterId'])) {
				$spotter_id = $data['data']['spotterId'];
				$content = print_r($raw, true);
				
				$wpdb->insert( 
					$wpdb->prefix . 'spoondrift_post_data', 
					array(
						'data' => $content
					),
					array( 
						'%s'
					)
				);
				
				// Run Process to process all unprocessed submissions
				uwa_process_spoondrift();
			}
			else {
				print 'Contains no Spotter ID';
			}
			
		}
		else {
			print 'Invalid JSON';
		}
	}

	// Removed from AJAX
	// Triggered internally after receiving new post data
	function uwa_fetch_spoondrift_wind_data_tester() {
		if(isset($_REQUEST['spotter_id'])) {
			uwa_fetch_spoondrift_wind_data_by_id($_REQUEST['spotter_id']);
		}
		else {
			print 'No spotter set';
		}

		wp_die();
	}

	add_action( 'wp_ajax_uwa_fetch_spoondrift_wind_data_tester', 'uwa_fetch_spoondrift_wind_data_tester' );
	add_action( 'wp_ajax_nopriv_uwa_fetch_spoondrift_wind_data_tester', 'uwa_fetch_spoondrift_wind_data_tester' );

	// Read one file a time
	// Receive JSON Data
	function uwa_process_directory_spoondrift_data() {
		global $wpdb;
	
		$content = '';

		$path = UWA__PLUGIN_DIR . 'modules/ajax/raw/';
		$raw = scandir($path);
		foreach($raw as $r) {
			if(strpos($r, '.json') !== false) {
				if(file_exists($path . $r)) {
					$raw = file_get_contents($path . $r);
					$json = json_decode($raw, true);

					uwa_receive_spoondrift_process_json($json, $raw);
					
					// Rename processed file
					$s = str_replace('.json', '.processed', $r);
					rename($path . $r, $path . $s);
				}
			}
		}
		
		wp_die();
	}

	add_action( 'wp_ajax_uwa_process_directory_spoondrift_data', 'uwa_process_directory_spoondrift_data' );
	add_action( 'wp_ajax_nopriv_uwa_process_directory_spoondrift_data', 'uwa_process_directory_spoondrift_data' );
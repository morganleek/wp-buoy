<?php
	// Receive JSON Data
	function uwa_receive_spoondrift_data() {
		global $wpdb;
	
		$content = '';
		
		$file_contents = file_get_contents('php://input');
		$data = json_decode($file_contents, true);
		if($data != null) {
			
			// Check for SpotterID
			if(isset($data['data']['spotterId'])) {
				$spotter_id = $data['data']['spotterId'];
				$content = print_r($file_contents, true);
				
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
		
		wp_die();
	}
	
	add_action( 'wp_ajax_uwa_receive_spoondrift_data', 'uwa_receive_spoondrift_data' );
	add_action( 'wp_ajax_nopriv_uwa_receive_spoondrift_data', 'uwa_receive_spoondrift_data' );

	// Removed from AJAX
	// Triggered internally after receiving new post data
	function uwa_fetch_spoondrift_wind_data_tester() {
		if(isset($_POST['spotter_id'])) {
			uwa_fetch_spoondrift_wind_data_by_id($_POST['spotter_id']);
		}
		else {
			print 'No spotter set';
		}

		wp_die();
	}

	add_action( 'wp_ajax_uwa_fetch_spoondrift_wind_data', 'uwa_fetch_spoondrift_wind_data' );
	add_action( 'wp_ajax_nopriv_uwa_fetch_spoondrift_wind_data', 'uwa_fetch_spoondrift_wind_data' );
<?php
	/* 
	 ** AJAX
	 ** Called via CRON
 	*/ 

	// // Cron Second Memplot, 1d and 2d
	// function cron_rev_update_datawell() {
	// 	uwa_datawell_grab_and_fill('Spec1D', 'spec_1d', '_Spec1D_');	
	// 	uwa_datawell_grab_and_fill('Spec2D', 'spec_2d', '_Spec2D_');
	// 	uwa_datawell_grab_and_fill('MEMplot', 'memplot', '_MEMplot_');
	// 	print 1;
	// 	wp_die();
 	// }
	
	// add_action( 'wp_ajax_update_datawell_rev', 'cron_rev_update_datawell' );
	// add_action( 'wp_ajax_nopriv_update_datawell_rev', 'cron_rev_update_datawell' );

	// Specrum 1D from AWS
	function update_datawell_1d() {
		uwa_log('datawell', 'Grad and Fill: "DatawellBuoys", "Spec1D", "datawell", "spec_1d", "_Spec1D_"');
		uwa_grab_and_fill('DatawellBuoys', 'Spec1D', 'datawell', 'spec_1d', '_Spec1D_');
		
		print 1;
		wp_die();
	}

	add_action( 'wp_ajax_update_datawell_1d', 'update_datawell_1d' );
	add_action( 'wp_ajax_nopriv_update_datawell_1d', 'update_datawell_1d' );

	// Specrum 2D from AWS
	function update_datawell_2d() {
		uwa_log('datawell', 'Grad and Fill: "DatawellBuoys", "Spec2D", "datawell", "spec_2d", "_Spec2D_"');
		uwa_grab_and_fill('DatawellBuoys', 'Spec2D', 'datawell', 'spec_2d', '_Spec2D_');
		
		print 1;
		wp_die();
	}

	add_action( 'wp_ajax_update_datawell_2d', 'update_datawell_2d' );
	add_action( 'wp_ajax_nopriv_update_datawell_2d', 'update_datawell_2d' );

	// Specrum Memplots from AWS
	function update_datawell_memplots() {
		uwa_log('datawell', 'Grad and Fill: "DatawellBuoys", "MEMplot", "datawell", "memplot", "_MEMplot_"');
		uwa_grab_and_fill('DatawellBuoys', 'MEMplot', 'datawell', 'memplot', '_MEMplot_');
		
		print 1;
		wp_die();
	}

	add_action( 'wp_ajax_update_datawell_memplots', 'update_datawell_memplots' );
	add_action( 'wp_ajax_nopriv_update_datawell_memplots', 'update_datawell_memplots' );


	

	// datawell version replaced by global above
	// function uwa_datawell_aws_direct($args, $return = false) {
	// 	$args['buoy_type'] = 'datawell';
	// 	uwa_aws_direct($args, $return);
	// }
	
	function uwa_datawell_aws() {
		$args = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		$args['buoy_type'] = 'datawell';

		uwa_aws_direct($args);
		
		wp_die();
	}
	
	add_action( 'wp_ajax_uwa_datawell_aws', 'uwa_datawell_aws' );
	add_action( 'wp_ajax_nopriv_uwa_datawell_aws', 'uwa_datawell_aws' );


 	// Cron Job Hook
 	function cron_update_datawell() {
		// Update run option	
		update_option('cron_updated_datawell', time());
				
		// Fetch CSVs
		// Only Check Every 24 Hours
		uwa_datawell_fetch_s3_file_list(true);
		
		// Process CSVs
		uwa_datawell_process_csvs();
		
		// Process Images
		// uwa_datawell_process_jpgs($files);
		
		print 1;
		
		wp_die();
 	}
 	
 	add_action( 'wp_ajax_update_datawell', 'cron_update_datawell' );
	add_action( 'wp_ajax_nopriv_update_datawell', 'cron_update_datawell' );
	
	function datawell_fetch_spectrum() {
		global $wpdb;
		
		$date = isset($_POST['date']) ? $_POST['date'] . ':00' : date('Y-m-d H:i:s'); // Dates don't have seconds
		$buoy_id = isset($_POST['buoy_id']) ? $_POST['buoy_id'] : 'Dev_site';
	
		$memplot = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}datawell_memplot
				WHERE `timestamp` >= '%s'
				AND `buoy_id` = '%s' 
				ORDER BY `timestamp` ASC LIMIT 1",
				$date,
				$buoy_id
			)
		);
		
		if($memplot) {
			print $memplot->url;
		}
		else {
			print 0;
		}
		
		wp_die();
	}
	
 	add_action( 'wp_ajax_fetch_spectrum', 'datawell_fetch_spectrum' );
	add_action( 'wp_ajax_nopriv_fetch_spectrum', 'datawell_fetch_spectrum' );
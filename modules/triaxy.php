<?php
 	// // Front end view functions
 	// require_once( UWA__PLUGIN_DIR . 'modules/views/view-datawell.php' );
 	// // Ajax
	// require_once( UWA__PLUGIN_DIR . 'modules/ajax/ajax-datawell.php' );

 	// define( 'triaxy_S3_URL' , 'https://s3-ap-southeast-2.amazonaws.com/uwawavebuoys/');

 	// add_shortcode('uwa_datawell', 'uwa_triaxy_manual');
 	// function uwa_triaxy_manual($atts, $content = null) {
 	// 	// uwa_triaxy_fetch_s3_file_list();
 	// 	// uwa_triaxy_process_csvs();
 		
 	// 	if(isset($_GET['buoy_id'])) {
	//  		uwa_triaxy_time_series_data($_GET['buoy_id'], null, null);
	//  	}
   // }

   function uwa_triaxy_ftp_fetch_and_store() {
     
   }
   
   // Returns a list of WAVE files
   // array(
   //   'serial' => Serial Number
   //   'timestamp' => Unix Time Stamp
   //   'size' => File Size
   //   'path' => File Path
   // )
   function uwa_triaxy_fetch_wave_file_list($serial = '', $ftp_server = '', $ftp_user_name = '', $ftp_user_pass = '') {
    if(!empty($serial) && !empty($ftp_server) && !empty($ftp_user_name) && !empty($ftp_user_pass)) {
      $root = "/" . $serial; // Serial Number
      
      // set up basic connection
      $conn_id = ftp_connect($ftp_server); 

      // login with username and password
      $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

      // check connection
      if ((!$conn_id) || (!$login_result)) { 
          echo "FTP connection has failed!";
          echo "Attempted to connect to $ftp_server for user $ftp_user_name<br>"; 
          exit; 
      } else {
          echo "Connected to $ftp_server, for user $ftp_user_name";
      }

      $fullWaves = array();

      // Read contents
      $rawYears = ftp_rawlist($conn_id, $root . "/"); // 
      if($rawYears) {
        // Fetch Years
        $years = processRawList($rawYears);
        foreach($years as $year) {
          $rawMonths = ftp_rawlist($conn_id, $root . "/" . $year['link'] . '/');
          if($rawMonths) {
            // Fetch Months
            $months = processRawList($rawMonths);
            foreach($months as $month) {
              $rawWaves = ftp_rawlist($conn_id, $root . "/" . $year['link'] . '/' . $month['link'] . '/' . 'WAVE' . '/');
              if($rawWaves) {
                $waves = processRawList($rawWaves);
                // _z($rawWaves);
                // ftp_close($conn_id);  die();
                foreach($waves as $wave) {
                  if(strlen($wave['link']) < 14) {
                    $fullWaves[] = array(
                      'serial' => $serial,
                      'timestamp' => $wave['timestamp'],
                      'size' => $wave['type'],
                      'path' => $root . "/" . $year['link'] . '/' . $month['link'] . '/' . 'WAVE' . '/' . $wave['link']
                    );
                  }
                }
              }
            }
          } 
        }
      }
      else {
        print 'Failed';
      }

      // close the FTP stream 
      ftp_close($conn_id); 

      return $fullWaves;
    }
  }
 	
 	// // Manually Trigger Process JPGs
 	// // function uwa_triaxy_process_jpgs_shortcode($atts, $content = null) {
	// //  	uwa_triaxy_process_jpgs();
 	// // }
 	// // add_shortcode('uwa_triaxy_process_jpgs_shortcode', 'uwa_triaxy_process_jpgs_shortcode');

 	// // Process Datawell CSVs
 	// function uwa_triaxy_process_csvs() {
	// 	ini_set("allow_url_fopen", 1);

 	// 	global $wpdb;

 	// 	$like = date('Y-m') . '.csv';
 	// 	$csvs = $wpdb->get_results("
 	// 		SELECT * FROM `wp_triaxy_csv` 
 	// 		WHERE `url` LIKE '%{0xF25}$like' 
 	// 		OR `url` LIKE '%{0xF26}$like' 
 	// 		OR `url` LIKE '%{0xF80}$like' 
 	// 	");
 		
 	// 	foreach($csvs as $csv) {
	//  		preg_match('/{(.*?)}/', $csv->url, $matches, PREG_OFFSET_CAPTURE);
	//  		if(sizeof($matches) == 2) {
	// 	 		$case = $matches[1][0];
		 		
	// 	 		// Download
	// 	 		$key = $csv->url;
	//  			$url = get_bloginfo('url') . '/wp-admin/admin-ajax.php?action=uwa_triaxy_aws&do=csv_fetch&key=' . $key; // 
	//  			$contents = file_get_contents($url);
	//  			// Split with line endings
	//  			$csv_content = str_getcsv($contents, "\n");
	 			
	//  			$inserts = array();
	//  			switch ($case) {
 	// 				case '0xF20':
 	// 				case '0xF21':
	// 				case '0xF23':
 	// 					break;
 	// 				case '0xF25': 
	//  					foreach($csv_content as $j => $csv_row) {
	//  						$inserts[$j]['buoy_id'] = "'" . $csv->buoy_id . "'";
	// 						$columns = explode("\t", $csv_row);
	// 						$time = round($columns[0] / 900) * 900; // Round time to cloest 30 minutes
	// 						$inserts[$j]['timestamp'] = "'" . date('Y-m-d H:i:s', $time) . "'";
	// 						// Significant Wave Height
	// 						$inserts[$j]['significant_wave_height'] = "'" . round($columns[3], 2) . "'";
	// 						// Peak Period
	// 						$inserts[$j]['peak_period'] = "'" . round($columns[11], 2) . "'";
	// 						// Mean Period
	// 						$inserts[$j]['mean_period'] = "'" . round($columns[6], 2) . "'";
	// 						// Peak Direction + Peak Spread
	// 						$peak_direction = round($columns[13] * 180 / M_PI, 2);
	// 						$inserts[$j]['peak_direction'] = "'" . $peak_direction . "'";
	// 						$peak_directional_spread = round($columns[14] * 180 / M_PI, 2);
	// 						$inserts[$j]['peak_directional_spread'] = "'" . $peak_directional_spread . "'";
	// 						// Mean Direction + Mean Spread
	// 						// $inserts[$j]['mean_direction'] = "'" . $columns[] . "'";
	// 						// $inserts[$j]['mean_directional_spread'] = "'" . $columns[] . "'";
	// 					}
 	// 					break;
 	// 				case '0xF26': 
 	// 					foreach($csv_content as $j => $csv_row) {
	//  						$inserts[$j]['buoy_id'] = "'" . $csv->buoy_id . "'";
	//  						$columns = explode("\t", $csv_row);
	//  						$time = round($columns[0] / 900) * 900; // Round time to cloest 30 minutes
 	// 						$inserts[$j]['timestamp'] = "'" . date('Y-m-d H:i:s', $time) . "'";
	//  						$inserts[$j]['max_wave_height'] = "'" . round($columns[2], 2) . "'";
 	// 					}
 	// 				case '0xF28':
 	// 				case '0xF29':
 	// 					break;
 	// 				case '0xF80': 
 	// 					foreach($csv_content as $j => $csv_row) {
 	// 						$inserts[$j]['buoy_id'] = "'" . $csv->buoy_id . "'";
 	// 						$columns = explode("\t", $csv_row);
 	// 						$time = round($columns[0] / 900) * 900; // Round time to cloest 30 minutes
 	// 						$inserts[$j]['timestamp'] = "'" . date('Y-m-d H:i:s', $time) . "'";
 	// 						$latitude = round($columns[2] * 180 / M_PI, 10);
 	// 						$inserts[$j]['latitude'] = "'" . $latitude . "'";
 	// 						$longitude = round($columns[3] * 180 / M_PI, 10);
 	// 						$inserts[$j]['longitude'] = "'" . $longitude . "'";
 	// 					}
 	// 					break;
 	// 				case '0xF81':
 	// 				case '0xF82':
 	// 				case '0xFC1':
 	// 				case '0xFC3':
 	// 					break;
 	// 				default:
 	// 					break;
 	// 			}
 				
 	// 			foreach($inserts as $insert) {
	//  				// Check if exists
	//  				$exists = $wpdb->get_results("SELECT * 
	//  					FROM " . $wpdb->prefix . "triaxy_post_data_processed_waves
	//  					WHERE `buoy_id` = " . $insert['buoy_id'] . "
	//  					AND `timestamp` = " . $insert['timestamp']);

	//  				if($wpdb->num_rows > 0) {
	//  					// Update
	//  					$key_value = '';
	//  					foreach($insert as $k => $i) {
	//  						if($key_value !== '') {
	//  							$key_value .= ', ';
	//  						}
	//  						$key_value .= ' ' . $k . '=' . $i . ' ';
	//  					}

	//  					$wpdb->query("UPDATE " . $wpdb->prefix . "triaxy_post_data_processed_waves SET " . $key_value . " WHERE `buoy_id` = " . $insert['buoy_id'] . " AND `timestamp` = " . $insert['timestamp']);
	//  				}
	//  				else {
	//  					$keys = implode(',', array_keys($insert));
	// 	 				$values = implode(',', $insert);

	//  					// Insert
	//  					$wpdb->query("INSERT INTO " . $wpdb->prefix . "triaxy_post_data_processed_waves ($keys) VALUES ($values)");
	//  				}

	//  			}
	//  		}
 	// 	}
 	// }

 	// // Fetch AWS S3 File List
 	// function uwa_triaxy_fetch_s3_file_list($return = false) {
	// 	// This will grab all the current CSVs.
 	// 	uwa_grab_and_fill_csv('waved', 'csv', 'datawell', 'csv', '');
 		
 	// 	return;
 	// }
 	
 	// function uwa_triaxy_grab_and_fill($type, $type_db, $date_sep) {
	// 	uwa_grab_and_fill('DatawellBuoys', $type, 'datawell', $type_db, $date_sep);
 	// }
 	
 	// /* 
 	// ** AJAX
 	// */ 
 	
 	// // Cron Job Hook
 	// function cron_update_datawell() {
	// 	// Update run option	
	// 	update_option('cron_updated_datawell', time());
		
	// 	// Fetch CSVs
	// 	// Only Check Every 24 Hours
	// 	$files = uwa_triaxy_fetch_s3_file_list(true);
		
	// 	// Process CSVs
	// 	uwa_triaxy_process_csvs();
		
	// 	// Process Images
	// 	// uwa_triaxy_process_jpgs($files);
		
	// 	print 1;
		
	// 	wp_die();
 	// }
 	
 	// add_action( 'wp_ajax_update_datawell', 'cron_update_datawell' );
	// add_action( 'wp_ajax_nopriv_update_datawell', 'cron_update_datawell' );
	
	// // Cron Second Memplot, 1d and 2d
 	// function cron_rev_update_datawell() {
	// 	uwa_triaxy_grab_and_fill('Spec1D', 'spec_1d', '_Spec1D_');
		
	// 	uwa_triaxy_grab_and_fill('Spec2D', 'spec_2d', '_Spec2D_');
		
	// 	uwa_triaxy_grab_and_fill('MEMplot', 'memplot', '_MEMplot_');
		
	// 	print 1;
		
	// 	wp_die();
 	// }
	
	// add_action( 'wp_ajax_update_triaxy_rev', 'cron_rev_update_datawell' );
	// add_action( 'wp_ajax_nopriv_update_triaxy_rev', 'cron_rev_update_datawell' );
	
	// function triaxy_fetch_spectrum() {
	// 	global $wpdb;
		
	// 	$date = isset($_POST['date']) ? $_POST['date'] . ':00' : date('Y-m-d H:i:s'); // Dates don't have seconds
	// 	$buoy_id = isset($_POST['buoy_id']) ? $_POST['buoy_id'] : 'Dev_site';
	
	// 	$memplot = $wpdb->get_row(
	// 		$wpdb->prepare(
	// 			"SELECT * FROM {$wpdb->prefix}triaxy_memplot
	// 			WHERE `timestamp` >= '%s'
	// 			AND `buoy_id` = '%s' 
	// 			ORDER BY `timestamp` ASC LIMIT 1",
	// 			$date,
	// 			$buoy_id
	// 		)
	// 	);
		
	// 	if($memplot) {
	// 		print $memplot->url;
	// 	}
	// 	else {
	// 		print 0;
	// 	}
		
	// 	wp_die();
	// }
	
 	// add_action( 'wp_ajax_fetch_spectrum', 'triaxy_fetch_spectrum' );
	// add_action( 'wp_ajax_nopriv_fetch_spectrum', 'triaxy_fetch_spectrum' );
 	
	class Triaxy_Plugin {
		static $instance;
	
		// class constructor
		public function __construct() {
			add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
			add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
		}
		
		public static function set_screen( $status, $option, $value ) {
			return $value;
		}
		
		public function plugin_menu() {
		
			$hook = add_submenu_page(
		    'uwa',
		    'Triaxy Dashboard',
		    'Triaxy',
		    'manage_options',
		    'uwa-triaxy',
		    [$this, 'plugin_settings_page']
		  );
	
			add_action( "load-$hook", [ $this, 'screen_option' ] );
		}
		
		/**
		 * Plugin settings page
		 */
		public function plugin_settings_page() {
			global $wpdb;
			
			// if(isset($_POST['refetch-csvs'])) {
			// 	uwa_triaxy_fetch_s3_file_list();
			// 	print '<div class="notice notice-success is-dismissible">';
	    //     print '<p>Refetched CSVs from S3</p>';
		  //   print '</div>';
			// }
			// if(isset($_POST['force-manual-processing'])) {
			// 	uwa_triaxy_process_csvs();
			// 	print '<div class="notice notice-success is-dismissible">';
	    //     print '<p>Processed All CSVs</p>';
		  //   print '</div>';
			// }
			// if(isset($_POST['cron-update-datawell'])) {
			// 	if(!wp_next_scheduled('cron_update_datawell')) {
			// 		wp_schedule_event( time(), 'hourly', 'cron_update_datawell' );
			// 	}
			// 	else {
			// 		wp_clear_scheduled_hook('cron_update_datawell');
			// 	}
			// }
			// //
			// // cron_update_datawell
			
			?>
			<div class="wrap">
				<h2>Spoondrift Events</h2>
	
				<table class="form-table">
					<tbody>
						<tr class="user-rich-editing-wrap">
							<th scope="row">FTP WAVE Files</th>
							<td>
								<?php 
									// $total_csv = $wpdb->get_var('SELECT COUNT(*) FROM `wp_triaxy_file_data`');
									// $processed_csv = $wpdb->get_var('SELECT COUNT(*) FROM `wp_triaxy_file_data` WHERE `processed` = 1');
									
									// print $total_csv . ' (' . $processed_csv . ' Processed)';
								?>
								<br><br>
								<form method="post" action="">
									<input type="hidden" name="refetch-csvs" value="refetch-csvs" />
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Refetch WAVEs from FTP">
								</form><br>
								<form method="post" action="">
									<input type="hidden" name="force-manual-processing" value="force-manual-processing" />
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Force Manual Processing">
								</form>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php
		}
		
		/**
		 * Screen options
		 */
		public function screen_option() {
			
		}
		
		/** Singleton instance */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
	
			return self::$instance;
		}
	}
	
	add_action( 'plugins_loaded', function () {
		Triaxy_Plugin::get_instance();
	} );
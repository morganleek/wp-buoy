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
  function uwa_extract_wave_data($cache = '') {
    if(!empty($cache)) {
      $contents = file($cache);

      $contents = array_slice($contents, 3);
      $exploded_contents = array();
      foreach($contents as $content) {
        $item_array = explode(' = ', $content);
        $item_array[0] = sanitize_title($item_array[0]);
        $exploded_contents[$item_array[0]] = $item_array[1];
      } 
      $exploded_contents['time'] = strtotime($exploded_contents['date']);
      
      return $exploded_contents;
    }  
    return array();
  }

  function uwa_process_raw_list($list) {
    $items = array();

    foreach($list as $list_item) {
      $list_item = str_replace(array('<', '>'), '', $list_item);
      $chunks = $chunks = preg_split("/\s+/", $list_item);
      // list($chunks['date'], $chunks['time'], $chunks['type'], $chunks['title']) = $chunks;

      // Convert Date to UNIX Timestamp
      // Make time 24 hrs
      $time = '';
      if(strpos($chunks[1], 'AM')) {
        $time = str_replace('AM', ':00', $chunks[1]);
        $time_exploded = explode(':', $time);
        $time_exploded[0] = sprintf('%02d', $time_exploded[0] % 12);
        $time = implode(':', $time_exploded);
      }
      else {
        $time_split = explode(':', str_replace('PM', '', $chunks[1]));
        $hour = (intval($time_split[0]) % 12) + 12;
        $time = $hour . ':' . $time_split[1] . ':00';
      }

      $year = explode('-', $chunks[0]);
      $year[2] += 2000;
      // $year = implode('-', array_reverse($year));
      $year = $year[2] . '-' . $year[0] . '-' . $year[1];

      $date = $year . ' ' . $time;
      $items[] = array(
        'date' => $date,
        'timestamp' => date('U', strtotime($date)),
        'type' => $chunks[2],
        'link' => $chunks[3]
      );
    }

    return $items;
  }

  function uwa_triaxy_fetch_servers() {
    $options = get_option('uwa_triaxy_ftps');
    if(!empty($options)) {
      $servers = json_decode( $options);
      return $servers->ftp;
    }
    return array();
  }

  function triaxy_test($atts, $content = '') {
    $count = 0;
    $limit = 5;
    $ftp_array = uwa_triaxy_fetch_servers();
    foreach($ftp_array as $ftp) {
      if($count >= $limit) {
        return;
      }
      $count = uwa_triaxy_fetch_wave_file_list($ftp->serial, $ftp->url, $ftp->user, $ftp->pass, $limit, $count);
    }
  }

  add_shortcode('triaxy_test', 'triaxy_test');

  // Grabs the next 'n' files that haven't been previously read and stores them
  function uwa_triaxy_fetch_wave_file_list($serial = '', $ftp_server = '', $ftp_user_name = '', $ftp_user_pass = '', $limit = 5, $count = 0) {
    if(!empty($serial) && !empty($ftp_server) && !empty($ftp_user_name) && !empty($ftp_user_pass)) {
      global $wpdb;

      // Fetch Buoy ID from Serial
      $serial_id = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "triaxy_serial_lookup WHERE `buoy_serial` = %s", $serial)
      );
  
      // If not ID create one
      if(empty($serial_id)) {
        if($wpdb->insert(
          $wpdb->prefix . "triaxy_serial_lookup", 
          array('buoy_serial' => $serial), 
          array('%s')
        )) {
          $serial_id = $wpdb->insert_id;
        }
        else {
          _d('Failed to insert serial');
        }
      }

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

      // $fullWaves = array();

      // Read contents
      $rawYears = ftp_rawlist($conn_id, $root . "/"); // 
      if($rawYears) {
        // Fetch Years
        $years = uwa_process_raw_list($rawYears);
        foreach($years as $year) {
          $rawMonths = ftp_rawlist($conn_id, $root . "/" . $year['link'] . '/');
          if($rawMonths) {
            // Fetch Months
            $months = uwa_process_raw_list($rawMonths);
            foreach($months as $month) {
              $rawWaves = ftp_rawlist($conn_id, $root . "/" . $year['link'] . '/' . $month['link'] . '/' . 'WAVE' . '/');
              if($rawWaves) {
                $waves = uwa_process_raw_list($rawWaves);
                // _z($rawWaves);
                // ftp_close($conn_id);  die();
                foreach($waves as $wave) {
                  if(strlen($wave['link']) > 14) {
                    if($count > $limit) {
                      ftp_close($conn_id); 
                      return 0;
                    }

                    $w = array(
                      'serial' => $serial,
                      'timestamp' => date('Y-m-d H:i:s', $wave['timestamp']),
                      'size' => $wave['type'],
                      'path' => $root . "/" . $year['link'] . '/' . $month['link'] . '/' . 'WAVE' . '/' . $wave['link']
                    );
                    // _d($w);

                    // Check this file hasn't been loaded before
                    $wpdb->get_results(
                      $wpdb->prepare("
                        SELECT * FROM " . $wpdb->prefix . "triaxy_ftp_wave_files 
                        WHERE `buoy_serial` = %d 
                        AND `timestamp` = '%s'", 
                        $serial_id, 
                        $w['timestamp']
                      )
                    );

                    if($wpdb->num_rows === 0) {
                      $cache = "cache.wave";
                      $remote = $w['path'];

                      $handle = fopen($cache, 'w');

                      if(ftp_fget($conn_id, $handle, $remote, FTP_ASCII, 0)) {
                        // Success
                      }
                      else {
                        return 0;
                      }

                      $wave = uwa_extract_wave_data($cache);

                      if(!empty($wave) && isset($wave['time'])) {
                        // Check if it exists
                        $wpdb->get_results("
                          SELECT * FROM " . $wpdb->prefix . "triaxy_post_data_processed_waves 
                          WHERE `buoy_serial_id` = %d 
                          AND `timestamp` = '%s'", 
                          $serial_id, 
                          $wave['time']
                        );

                        if($wpdb->num_rows === 0) {
                          // Insert it
                          $wpdb->insert($wpdb->prefix . "triaxy_post_data_processed_waves",
                            array(
                              'buoy_serial_id' => $serial_id,
                              'number_of_zero_crossings'=> empty($wave['number-of-zero-crossings']) ? 0 : $wave['number-of-zero-crossings'],
                              'average_wave_height'=> empty($wave['average-wave-height-havg']) ? 0 : $wave['average-wave-height-havg'],
                              't_avg'=> empty($wave['tavg']) ? 0 : $wave['tavg'],
                              'max_wave_height'=> empty($wave['max-wave-height-hmax']) ? 0 : $wave['max-wave-height-hmax'],
                              't_max'=> empty($wave['tmax']) ? 0 : $wave['tmax'],
                              'significant_wave_height'=> empty($wave['significant-wave-height-hsig']) ? 0 : $wave['significant-wave-height-hsig'],
                              'significant_wave_peroid'=> empty($wave['significant-wave-period-tsig']) ? 0 : $wave['significant-wave-period-tsig'],
                              'h10'=> empty($wave['h10']) ? 0 : $wave['h10'],
                              't10'=> empty($wave['t10']) ? 0 : $wave['t10'],
                              'peak_crest'=> empty($wave['peak-crest']) ? 0 : $wave['peak-crest'],
                              'mean_period'=> empty($wave['mean-period']) ? 0 : $wave['mean-period'],
                              'peak_period'=> empty($wave['peak-period']) ? 0 : $wave['peak-period'],
                              'peak_direction'=> empty($wave['peak-direction']) ? 0 : $wave['peak-direction'],
                              'peak_directional_spread'=> empty($wave['peak-spread']) ? 0 : $wave['peak-spread'],
                              'tp5'=> empty($wave['tp5']) ? 0 : $wave['tp5'],
                              'hm0'=> empty($wave['hm0']) ? 0 : $wave['hm0'],
                              'mean_direction'=> empty($wave['mean-magnetic-direction']) ? 0 : $wave['mean-magnetic-direction'],
                              'mean_directional_spread'=> empty($wave['mean-spread']) ? 0 : $wave['mean-spread'],
                              'te'=> empty($wave['te']) ? 0 : $wave['te'],
                              'wave_steepness'=> empty($wave['wave-steepness']) ? 0 : $wave['wave-steepness'],
                              'timestamp'=> date('Y-m-d H:i:s', $wave['time'])
                            ),
                            array(
                              '%d', '%d', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%s' 
                            )                      
                          );

                          // Add them to the files received record
                          $wpdb->insert($wpdb->prefix . "triaxy_ftp_wave_files",
                            array(
                              'buoy_serial' => $serial_id,
                              'timestamp' => $w['timestamp'],
                              'size' => $w['size'],
                              'file_path' => $w['path']
                            ),
                            array(
                              '%d', '%s', '%d', '%s'
                            )
                          );
                        }
                      }

                      $count++;
                    }
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

      return $count; // $fullWaves;
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
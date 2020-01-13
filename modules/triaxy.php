<?php
 	// Front end view functions
 	require_once( UWA__PLUGIN_DIR . 'modules/views/view-triaxy.php' );
 	// // Ajax
	// require_once( UWA__PLUGIN_DIR . 'modules/ajax/ajax-triaxy.php' );

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
                        $wpdb->get_results(
                          $wpdb->prepare(
                            "SELECT * FROM " . $wpdb->prefix . "triaxy_post_data_processed_waves 
                            WHERE `buoy_serial_id` = %d 
                            AND `timestamp` = '%s'", 
                            $serial_id, 
                            $wave['time']
                          )
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
 	
 	// /* 
 	// ** AJAX
 	// */ 
 	
 	// Cron Job Hook
 	function cron_update_triaxy() {
		// Update run option	
		$count = 0;
    $limit = 24;
    $ftp_array = uwa_triaxy_fetch_servers();
    foreach($ftp_array as $ftp) {
      if($count >= $limit) {
        return;
      }
      $limit = uwa_triaxy_fetch_wave_file_list($ftp->serial, $ftp->url, $ftp->user, $ftp->pass, $limit, $count);
    }
		
		print 1;
		
		wp_die();
 	}
 	
 	add_action( 'wp_ajax_update_triaxy', 'cron_update_triaxy' );
	add_action( 'wp_ajax_nopriv_update_triaxy', 'cron_update_triaxy' );
 	
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
			
			if(isset($_POST['force-manual-fetch'])) {
				cron_update_triaxy();
				print '<div class="notice notice-success is-dismissible">';
	        print '<p>Processed All CSVs</p>';
		    print '</div>';
			}
			
			?>
			<div class="wrap">
				<h2>Triaxy Events</h2>
	
				<table class="form-table">
					<tbody>
						<tr class="user-rich-editing-wrap">
							<th scope="row">FTP WAVE Files</th>
							<td>
                <?php
                  $total = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "triaxy_post_data_processed_waves");
                  print "Processed (" . $total . ")";
                ?>
                <br><br>
								<form method="post" action="">
									<input type="hidden" name="force-manual-fetch" value="force-manual-fetch" />
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Force Manual Fetch">
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
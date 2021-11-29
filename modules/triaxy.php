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

  // function triaxy_test($atts, $content = '') {
  //   $exhausted = uwa_triaxy_check_folder(array(
  //     'folder' => "/G3-13844157117119177437/2019/",
  //     'folder_size' => 3
  //   ));
  //   var_dump($exhausted);
  // }

  // add_shortcode('triaxy_test', 'triaxy_test');

  function uwa_triaxy_check_folder($args = array(), &$log) {
    global $wpdb;

    $defaults = array(
      'root' => '',
			'folder' => '', 
      'folder_size' => 0,
      'offset' => '+31 days' // 1 day for waves, 32 days for folders
		);

    $_args = wp_parse_args($args, $defaults);
    extract($_args);

    if($folder != '') {
      // See if this folder is listed
      $this_folder = $wpdb->get_row(
        $wpdb->prepare(
          "SELECT * FROM " . $wpdb->prefix . "triaxy_ftp_folder 
          WHERE `file_path` = '%s'
          LIMIT 1",
          $folder
        )
      );
      $rows = $wpdb->num_rows;

      // Already complete
      if($rows > 0 && $this_folder->complete == '1') {
        $log[] = time() . ": " . "Already marked complete " . $folder;
      }

      // if($rows > 0 && (strtotime($this_folder->timestamp) < time()) && $this_folder->total_files == $folder_size) {
      //   // This folder has had n day(s) without updates
      //   // Complete this folder
      //   $log[] = time() . ": " . "Mark complete " . $folder;
      //   $wpdb->update(
      //     $wpdb->prefix . "triaxy_ftp_folder",
      //     array('complete' => 1),
      //     array('id' => $this_folder->id),
      //     array('%d'),
      //     array('%d')
      //   );

      //   return true;
      // }
      // else if($rows > 0 && (strtotime($this_folder->timestamp) < time())) {
      //   // This folder has been checked within the last day but the files have changed
      //   // Update the date
      //   $log[] = time() . ": " . "File number changed " . $folder;
      //   $wpdb->update(
      //     $wpdb->prefix . "triaxy_ftp_folder",
      //     array(
      //       'total_files' => $folder_size,
      //       'timestamp' => date('Y-m-d H:i:s', strtotime($offset))
      //     ),
      //     array('id' => $this_folder->id),
      //     array('%d', '%s'),
      //     array('%d')
      //   );
      // }

      if($rows > 0) {
        // Check if timestamp has expired and mark complete
        if(strtotime($this_folder->timestamp) < time()) {
          // Complete this folder
          $log[] = time() . ": " . "Mark complete " . $folder;
          $wpdb->update(
            $wpdb->prefix . "triaxy_ftp_folder",
            array('complete' => 1),
            array('id' => $this_folder->id),
            array('%d'),
            array('%d')
          );
        }
        else {
          // Check for new files and update numbers
          $log[] = time() . ": " . "File number changed " . $folder;
          $wpdb->update(
            $wpdb->prefix . "triaxy_ftp_folder",
            array(
              'total_files' => $folder_size
            ),
            array('id' => $this_folder->id),
            array('%d'),
            array('%d')
          );
        }
      }
      else if($rows === 0) {
        // Wait to ensure limit has been reached
        // before adding folder record
        $log[] = time() . ": " . "Folder added to watch " . $folder;

        $wpdb->insert(
          $wpdb->prefix . "triaxy_ftp_folder",
          array(
            'file_path' => $folder,
            'total_files' => $folder_size,
            'timestamp' => date('Y-m-d H:i:s', strtotime($offset)),
            'complete' => 0
          ),
          array(
            '%s', 
            '%d', 
            '%s',
            '%d'
          )
        );
      }
    }

    return false;
  }

  // Grabs the next 'n' files that haven't been previously read and stores them
  function uwa_triaxy_fetch_wave_file_list($args = array()) {
    $defaults = array(
      'serial' => '',
      'ftp_server' => '', 
      'ftp_user_name' => '', 
      'ftp_user_pass' => '',
      'limit' => 5,
      'count' => 0,
      'verbose' => false
    );
    $_args = wp_parse_args($args, $defaults);
    extract($_args);

    if(!empty($serial) && !empty($ftp_server) && !empty($ftp_user_name) && !empty($ftp_user_pass)) {
      global $wpdb;

      $_br = "&#13;&#10;";
      // $triaxy_ftp_log = get_option('triaxy_ftp_log', ''); // implode($_br, array_slice(explode($_br, get_option('triaxy_ftp_log', '')), 0, 500));

      $log = array();
      $log[] = ''; // Empty space
      $log[] = time() . ": Initiating " . $ftp_server;

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
          // $triaxy_ftp_log .= time() . ":" . "Failed to insert serial $_br";
          $log[] = time() . ": Failed to insert serial";
        }
      }

      $root = "/" . $serial; // Serial Number

      // set up basic connection
      $conn_id = ftp_connect($ftp_server); 

      // login with username and password
      $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

      // check connection
      if ((!$conn_id) || (!$login_result)) { 
        // $triaxy_ftp_log .= time() . ":" . "FTP connection has failed! $_br";
        $log[] = time() . ": FTP connection has failed!";
        // $triaxy_ftp_log .= time() . ":" . "Attempted to connect to $ftp_server for user $ftp_user_name $_br";
        $log[] = time() . ": Attempted to connect to $ftp_server for user $ftp_user_name";
        if($verbose) {
          print implode('<br>', $log);
        }
        return;
      } else {
        // $triaxy_ftp_log .= time() . ":" . "Connected to $ftp_server, for user $ftp_user_name $_br";
        $log[] = time() . ": Connected to $ftp_server, for user $ftp_user_name";
      }

      // $fullWaves = array();

      // Read contents
      // $triaxy_ftp_log .= time() . ":" . "Reading " . $root . "/ $_br";
      $log[] = time() . ": Reading " . $root . "/";
      $rawYears = ftp_rawlist($conn_id, $root . "/"); // 
      if($rawYears) {
        // Fetch Years
        $years = uwa_process_raw_list($rawYears);
        foreach($years as $year) {
          // $triaxy_ftp_log .= time() . ":" . "Reading Year " . $root . "/" . $year['link'] . "/ $_br";
          $log[] = time() . ": Reading Year " . $root . "/" . $year['link'] . "/";
          $rawMonths = ftp_rawlist($conn_id, $root . "/" . $year['link'] . "/");

          if($rawMonths) {
            // Fetch Months
            $months = uwa_process_raw_list($rawMonths);

            $y_exhausted = uwa_triaxy_check_folder(array(
              'root' => $root,
              'folder' => $root . "/" . $year['link'] . "/",
              'folder_size' => count($year),
              'offset' => '+366 days'
            ), $log);

            if(!$y_exhausted) {
              foreach($months as $month) {
                // Check if month already completed
                $months_check = $wpdb->get_row(
                  $wpdb->prepare(
                    "SELECT COUNT(*) AS `total` 
                    FROM " . $wpdb->prefix . "triaxy_ftp_folder 
                    WHERE `file_path` = '%s'
                    AND `complete` = 1
                    LIMIT 1",
                    $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/"
                  )
                );

                // Check if completed already
                if($months_check->total == 0) {
                  // $triaxy_ftp_log .= time() . ":" . "Reading Month " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/ $_br";
                  $log[] = time() . ": Reading Month " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/";
                  $rawWaves = ftp_rawlist($conn_id, $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/");
                  if($rawWaves) {
                    $waves = uwa_process_raw_list($rawWaves);

                    // Check if folders exhausted
                    // +2 days as buoys can sometimes stop sending data for prolonged periods
                    $m_exhausted = uwa_triaxy_check_folder(array(
                      'root' => $root,
                      'folder' => $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/",
                      'folder_size' => count($waves),
                      'offset' => '+5 days' 
                    ), $log);

                    if(!$m_exhausted) {
                      // Do the fetch
                      // Check again if it's not been a day since the last check
                      foreach($waves as $wave) {
                        if(strlen($wave['link']) > 14) {
                          if($count > $limit) {
                            // $triaxy_ftp_log .= time() . ":" . "Count Limit Reached $_br";
                            $log[] = time() . ": Count Limit Reached";
                            ftp_close($conn_id); 
                            update_option( 'triaxy_ftp_log', $triaxy_ftp_log . $_br . implode($_br, $log));
                            if($verbose) {
                              print implode('<br>', $log);
                            }
                            return 0;
                          }

                          // $triaxy_ftp_log .= time() . ":" . "Checking if scanned " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/" . $wave['link'] . " $_br";
                          // $log[] = time() . ": Checking if scanned " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/" . $wave['link'];

                          $w = array(
                            'serial' => $serial,
                            'timestamp' => date('Y-m-d H:i:s', $wave['timestamp']),
                            'size' => $wave['type'],
                            'path' => $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/" . $wave['link']
                          );

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
                            // $triaxy_ftp_log .= time() . ":" . "Not scanned ... downloading $_br";
                            $log[] = time() . ": Checking if scanned " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/" . $wave['link'];
                            $log[] = time() . ": Not scanned ... downloading";

                            $cache = "cache.wave";
                            $remote = $w['path'];

                            $handle = fopen($cache, 'w');

                            if(ftp_fget($conn_id, $handle, $remote, FTP_ASCII, 0)) {
                              // Success
                              // $triaxy_ftp_log .= time() . ":" . "Downloaded $_br";
                              $log[] = time() . ": Downloaded";
                            }
                            else {
                              // $triaxy_ftp_log .= time() . ":" . "Download failed $_br";
                              $log[] = time() . ": Download failed";
                              // update_option( 'triaxy_ftp_log', $triaxy_ftp_log . $_br . implode($_br, $log));
                              // return -2;
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

                              // $triaxy_ftp_log .= time() . ":" . "Exists? $_br";
                              $log[] = time() . ": Exists?";

                              if($wpdb->num_rows === 0) {
                                // $triaxy_ftp_log .= time() . ":" . "No, inserting and marking as read $_br";
                                $log[] = time() . ": No, inserting and marking as read";
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
                              else {
                                // $triaxy_ftp_log .= time() . ":" . "Exists $_br";
                                $log[] = time() . ": Exists";
                              }
                            }

                            $count++;
                          }
                          else {
                            // $triaxy_ftp_log .= time() . ":" . "Already scanned $_br";
                            $log[] = time() . ": Already scanned";
                          }
                        }
                      }
                    }
                    else {
                      $log[] = time() . ': Month has been exhausted';
                    }
                  }
                }
                else {
                  // $triaxy_ftp_log .= time() . ":" . "Skipping " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/ $_br";
                  $log[] = time() . ": Skipping " . $root . "/" . $year['link'] . "/" . $month['link'] . "/" . 'WAVE' . "/";
                }
              }
            }
            else {
              $log[] = time() . ': Year folder has been exhausted';
            }
          } 
          else {
            $log[] = time() . ": Directory is empty";
          }
        }
      }
      else {
        // $triaxy_ftp_log .= time() . ":" . "Failed $_br";
        $log[] = time() . ": Connection Failed " . $root . "/";
      }

      // close the FTP stream 
      ftp_close($conn_id); 

      update_option( 'triaxy_ftp_log', $triaxy_ftp_log . $_br . implode($_br, $log));

      if($verbose) {
        print implode('<br>', $log);
      }

      return $count; // $fullWaves;
    }
  }
 	
 	// /* 
 	// ** AJAX
 	// */ 
 	
   // Cron Job Hook
  function uwa_update_triaxy($verbose = false) {
    update_option('triaxy_ftp_log', ''); // Clear log

		// Update run option	
		$count = 0;
    $limit = 24; // Days worth
    $ftp_array = uwa_triaxy_fetch_servers();
    foreach($ftp_array as $ftp) {
      if($count >= $limit) {
        return;
      }
      $count = uwa_triaxy_fetch_wave_file_list( 
        array(
          'serial' => $ftp->serial, 
          'ftp_server' => $ftp->url, 
          'ftp_user_name' => $ftp->user, 
          'ftp_user_pass' => $ftp->pass, 
          'limit' => $limit, 
          'count' => $count,
          'verbose' => $verbose
        )
      );
    }
  }

 	function cron_update_triaxy() {
    $verbose = isset($_GET['verbose']) ?: false;

    uwa_update_triaxy($verbose);
		
		wp_die();
 	}
 	
 	add_action( 'wp_ajax_update_triaxy', 'cron_update_triaxy' );
  add_action( 'wp_ajax_nopriv_update_triaxy', 'cron_update_triaxy' );
  
  // function shortcode_cron_update_triaxy($atts, $content = null) {
  //   cron_update_triaxy();
  // }

  // add_shortcode( 'tiaxy_test', 'shortcode_cron_update_triaxy');
 	
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
				uwa_update_triaxy();
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
            <tr class="user-rich-editing-wrap">
							<th scope="row">FTP Transfer Log<br><em>(Last 500 lines)</em></th>
							<td>
                <textarea name="triaxy_ftp_log" rows="10" cols="100" id="triaxy_ftp_log" class="text-large code"><?php print get_option('triaxy_ftp_log', '...'); ?></textarea>
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
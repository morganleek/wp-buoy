<?php
 	// Front end view functions
 	require_once( UWA__PLUGIN_DIR . 'modules/views/view-datawell.php' );
 	// Ajax
	require_once( UWA__PLUGIN_DIR . 'modules/ajax/ajax-datawell.php' );

 	define( 'DATAWELL_S3_URL' , 'https://s3-ap-southeast-2.amazonaws.com/uwawavebuoys/');

 	add_shortcode('uwa_datawell', 'uwa_datawell_manual');
 	function uwa_datawell_manual($atts, $content = null) {
 		// uwa_datawell_fetch_s3_file_list();
 		// uwa_datawell_process_csvs();
 		
 		if(isset($_GET['buoy_id'])) {
	 		uwa_datawell_time_series_data($_GET['buoy_id'], null, null);
	 	}
 	}
 	
 	// Process Datawell JPGs
	/*
 	function uwa_datawell_process_jpgs($files = null) {
	 	ini_set("allow_url_fopen", 1);

 		global $wpdb;
 		
 		// $last_file = ""; // Repeat incase max results have come back
 		if($files === null) {
	 		// Get last file records that hasn't been modified in 32 days
	 		// $recent = $wpdb->get_row('SELECT * FROM `wp_datawell_file_data` WHERE `last_modified` < NOW() - INTERVAL 32 DAY ORDER BY `last_modified` DESC LIMIT 1');
	 		// $last_file = $recent->url;
	 		
	 		// while(!empty($last_file)) {
	 		// Fetch S3 Files
	 		// $url = get_bloginfo('url') . '/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=fetch_after&previous=' . $last_file;
	 		$url = get_bloginfo('url') . '/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=fetch_all';
	 		$json = file_get_contents($url);
			$files = json_decode($json);
		}
		
		// var_dump($files);
		
		if(!empty($files)) {
 			foreach($files as $file_a) {
	 			$file = $file_a[0];
	 			$last_modified = date('Y-m-d H:i:s', strtotime($file_a[1]));

 				if(strpos($file, 'jpg') !== false) {
 					$exploded = explode('/', $file);
 					
 					// Time
 					$last = array_pop($exploded);
 					$last_exploded = explode('_', $last);
 					
 					$date = str_split($last_exploded[2]);
 					$time = str_split(str_replace('.jpg', '', $last_exploded[3]));
 					$date_time = $date[0] . $date[1] . $date[2] . $date[3] . '-' . $date[4] . $date[5] . '-' . $date[6] . $date[7] . ' ' . $time[0] . $time[1] . ':' . $time[2] . $time[3] . ':00';
 					
 					// Buoy
 					$buoy = explode('_', array_pop($exploded));
 					$buoy_title = $buoy[1];
 					
 					// Hack
 					$buoy_title = str_replace('DevSite', 'Dev_Site', $buoy_title);
 					
 					
 					// Check a record exists
 					$row = $wpdb->get_row(
 						$wpdb->prepare(
 							"SELECT * FROM " . $wpdb->prefix . "datawell_post_data_processed_waves WHERE `timestamp` = '%s' AND `buoy_id` = '%s' AND `memplot` IS NULL LIMIT 1",
 							$date_time,
 							$buoy_title
						)
					);
					if($row) {
						// Add file when the record exists
						$wpdb->update(
							$wpdb->prefix . 'datawell_post_data_processed_waves',
							array(
								'memplot' => $file
							),
							array(
								'id' => $row->id
							),
							array('%s'),
							array('%d')
						);
					}
					
					$last_file = $file;
	 			}
	 		}
	 	}
	 	else {
		 	$last_file = "";
		}
		// }
 	}
	*/
 	
 	// Manually Trigger Process JPGs
 	// function uwa_datawell_process_jpgs_shortcode($atts, $content = null) {
	//  	uwa_datawell_process_jpgs();
 	// }
 	// add_shortcode('uwa_datawell_process_jpgs_shortcode', 'uwa_datawell_process_jpgs_shortcode');

 	// Process Datawell CSVs
 	function uwa_datawell_process_csvs() {
		ini_set("allow_url_fopen", 1);

 		global $wpdb;

 		$like = date('Y-m') . '.csv';
 		$csvs = $wpdb->get_results("
 			SELECT * FROM `wp_datawell_csv` 
 			WHERE `url` LIKE '%{0xF25}$like' 
 			OR `url` LIKE '%{0xF26}$like' 
 			OR `url` LIKE '%{0xF80}$like' 
 		");
 		
 		foreach($csvs as $csv) {
	 		preg_match('/{(.*?)}/', $csv->url, $matches, PREG_OFFSET_CAPTURE);
	 		if(sizeof($matches) == 2) {
		 		$case = $matches[1][0];
		 		
		 		// Download
		 		$key = $csv->url;
	 			$url = get_bloginfo('url') . '/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=csv_fetch&key=' . $key; // 
	 			$contents = file_get_contents($url);
	 			// Split with line endings
	 			$csv_content = str_getcsv($contents, "\n");
	 			
	 			$inserts = array();
	 			switch ($case) {
 					case '0xF20':
 					case '0xF21':
					case '0xF23':
 						break;
 					case '0xF25': 
	 					foreach($csv_content as $j => $csv_row) {
	 						$inserts[$j]['buoy_id'] = "'" . $csv->buoy_id . "'";
							$columns = explode("\t", $csv_row);
							$time = round($columns[0] / 900) * 900; // Round time to cloest 30 minutes
							$inserts[$j]['timestamp'] = "'" . date('Y-m-d H:i:s', $time) . "'";
							// Significant Wave Height
							$inserts[$j]['significant_wave_height'] = "'" . round($columns[3], 2) . "'";
							// Peak Period
							$inserts[$j]['peak_period'] = "'" . round($columns[11], 2) . "'";
							// Mean Period
							$inserts[$j]['mean_period'] = "'" . round($columns[6], 2) . "'";
							// Peak Direction + Peak Spread
							$peak_direction = round($columns[13] * 180 / M_PI, 2);
							$inserts[$j]['peak_direction'] = "'" . $peak_direction . "'";
							$peak_directional_spread = round($columns[14] * 180 / M_PI, 2);
							$inserts[$j]['peak_directional_spread'] = "'" . $peak_directional_spread . "'";
							// Mean Direction + Mean Spread
							// $inserts[$j]['mean_direction'] = "'" . $columns[] . "'";
							// $inserts[$j]['mean_directional_spread'] = "'" . $columns[] . "'";
						}
 						break;
 					case '0xF26': 
 						foreach($csv_content as $j => $csv_row) {
	 						$inserts[$j]['buoy_id'] = "'" . $csv->buoy_id . "'";
	 						$columns = explode("\t", $csv_row);
	 						$time = round($columns[0] / 900) * 900; // Round time to cloest 30 minutes
 							$inserts[$j]['timestamp'] = "'" . date('Y-m-d H:i:s', $time) . "'";
	 						$inserts[$j]['max_wave_height'] = "'" . round($columns[2], 2) . "'";
 						}
 					case '0xF28':
 					case '0xF29':
 						break;
 					case '0xF80': 
 						foreach($csv_content as $j => $csv_row) {
 							$inserts[$j]['buoy_id'] = "'" . $csv->buoy_id . "'";
 							$columns = explode("\t", $csv_row);
 							$time = round($columns[0] / 900) * 900; // Round time to cloest 30 minutes
 							$inserts[$j]['timestamp'] = "'" . date('Y-m-d H:i:s', $time) . "'";
 							$latitude = round($columns[2] * 180 / M_PI, 10);
 							$inserts[$j]['latitude'] = "'" . $latitude . "'";
 							$longitude = round($columns[3] * 180 / M_PI, 10);
 							$inserts[$j]['longitude'] = "'" . $longitude . "'";
 						}
 						break;
 					case '0xF81':
 					case '0xF82':
 					case '0xFC1':
 					case '0xFC3':
 						break;
 					default:
 						break;
 				}
 				
 				foreach($inserts as $insert) {
	 				// Check if exists
	 				$exists = $wpdb->get_results("SELECT * 
	 					FROM " . $wpdb->prefix . "datawell_post_data_processed_waves
	 					WHERE `buoy_id` = " . $insert['buoy_id'] . "
	 					AND `timestamp` = " . $insert['timestamp']);

	 				if($wpdb->num_rows > 0) {
	 					// Update
	 					$key_value = '';
	 					foreach($insert as $k => $i) {
	 						if($key_value !== '') {
	 							$key_value .= ', ';
	 						}
	 						$key_value .= ' ' . $k . '=' . $i . ' ';
	 					}

	 					$wpdb->query("UPDATE " . $wpdb->prefix . "datawell_post_data_processed_waves SET " . $key_value . " WHERE `buoy_id` = " . $insert['buoy_id'] . " AND `timestamp` = " . $insert['timestamp']);
	 				}
	 				else {
	 					$keys = implode(',', array_keys($insert));
		 				$values = implode(',', $insert);

	 					// Insert
	 					$wpdb->query("INSERT INTO " . $wpdb->prefix . "datawell_post_data_processed_waves ($keys) VALUES ($values)");
	 				}

	 			}
	 		}
 		}
 	}

 	// Fetch AWS S3 File List
 	function uwa_datawell_fetch_s3_file_list($return = false) {
		// This will grab all the current CSVs.
 		uwa_grab_and_fill_csv('waved', 'csv', 'datawell', 'csv', '');
 		
 		return;
 	}
 	
 	function uwa_datawell_grab_and_fill($type, $type_db, $date_sep) {
		uwa_grab_and_fill('DatawellBuoys', $type, 'datawell', $type_db, $date_sep);
 	}
 	
	class Datawell_Plugin {
		// class instance
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
		    'Datawell Dashboard',
		    'Datawell',
		    'manage_options',
		    'uwa-datawell',
		    [$this, 'plugin_settings_page']
		  );
	
			add_action( "load-$hook", [ $this, 'screen_option' ] );
	
		}
		
		/**
		 * Plugin settings page
		 */
		public function plugin_settings_page() {
			global $wpdb;
			
			if(isset($_POST['refetch-csvs'])) {
				uwa_datawell_fetch_s3_file_list();
				print '<div class="notice notice-success is-dismissible">';
	        print '<p>Refetched CSVs from S3</p>';
		    print '</div>';
			}
			if(isset($_POST['force-manual-processing'])) {
				uwa_datawell_process_csvs();
				print '<div class="notice notice-success is-dismissible">';
	        print '<p>Processed All CSVs</p>';
		    print '</div>';
			}
			if(isset($_POST['cron-update-datawell'])) {
				if(!wp_next_scheduled('cron_update_datawell')) {
					wp_schedule_event( time(), 'hourly', 'cron_update_datawell' );
				}
				else {
					wp_clear_scheduled_hook('cron_update_datawell');
				}
			}
			//
			// cron_update_datawell
			
			?>
			<div class="wrap">
				<h2>Spoondrift Events</h2>
	
				<table class="form-table">
					<tbody>
						<tr class="user-rich-editing-wrap">
							<th scope="row">Cron Scheduling</th>
							<td>
								<?php 
									$button = '';
									if(wp_next_scheduled('cron_update_datawell')) {
										$button = 'Deactivate Cron';
										
										print 'Last S3 Fetch: ';
										if(get_option('cron_updated_datawell')) {
											print get_option('cron_updated_datawell');
										}
										else {
											print 'Never';
										}
										print '<br><br>';
										print 'Next S3 Fetch: ' . date('d-m-Y H:i:s', wp_next_scheduled('cron_update_datawell')) . 'UTC<br><br>';
									}
									else {
										$button = 'Activate Cron';
										print 'Cron is not activated<br><br>';
									}
								?>
								<form method="post" action="">
									<input type="hidden" name="cron-update-datawell" value="cron-update-datawell" />
									<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php print $button; ?>">
								</form>
							</td>
						</tr>
						<tr class="user-rich-editing-wrap">
							<th scope="row">S3 CSV Files</th>
							<td>
								<form method="post" action="">
									<input type="hidden" name="refetch-csvs" value="refetch-csvs" />
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Refetch CSVs from S3">
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
		Datawell_Plugin::get_instance();
	} );
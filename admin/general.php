<?php 
	// Grab Memplot, 1d and 2d Specrum Data
	function uwa_grab_and_fill($bucket, $type, $parent_db, $type_db, $date_sep) {
	 	// $bucket = 'DatawellBuoys'
	 	// $type = 'Spec1D'
	 	// $parent_db = 'datawell'
	 	// $type_db = '_spec_1D_'
	 	// $date_sep = '_Spec1D_'
	 	
	 	global $wpdb;
			
		$prefix = '';

		// $buoys = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . $parent_db . "_lookup");	
		$buoys = $wpdb->get_results("SELECT * FROM `wp_buoy_info` WHERE `buoy_type` = '" . $parent_db . "'");

		uwa_log($parent_db, 'Buoys found: ' . $wpdb->num_rows);
		
		foreach($buoys as $buoy) {
			$buoy_id = $buoy->buoy_id;
			$label = $buoy->aws_label;
			
			$prefix = trailingslashit($bucket) . trailingslashit($label) . $type;
			
			$query = array(
				'action=uwa_datawell_aws', // Global AWS Fetch
				'do=fetch_after_prefix', 
				'max-keys=48',
				'buoy_type=' . $parent_db,
				'prefix=' . $prefix
			);
			
			// Check for last file 
			$previous = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM " . $wpdb->prefix . $parent_db . "_" . $type_db . " 
					WHERE buoy_id = '%s' ORDER BY timestamp DESC LIMIT 1",
					$buoy_id
				)
			);
			if($wpdb->num_rows !== 0) {
				$query[] = 'previous=' . $previous->url;
			}
			
			uwa_log($parent_db, 'AWS query prefix: ' . implode('&', $query));
			// $url = get_bloginfo('url') . '/wp-admin/admin-ajax.php?' . implode('&', $query);
			// $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
			// $json = file_get_contents($url, false, $context);
			// $files = json_decode($json);
			$json = uwa_aws_direct(
				uwa_query_array_to_key_value($query), 
				true
			);
			$files = json_decode($json['html']);

			// log the number of files
			uwa_log($parent_db, 'Files found: ' . sizeof($files));
			
			if(!empty($files)) {
				foreach($files as $file_a) {
					$url = $file_a[0];
					if(strpos($url, '.csv') > 0 || strpos($url, '.jpg') > 0) {
						$time = explode($date_sep, $url);
						$time_file = array_pop($time);
						$v = str_replace('.csv', '', $time_file);
						$v = str_replace('.jpg', '', $v);
						$v = str_replace('_', ' ', $v);
						$v = str_replace('UTC', '', $v);
						$timestamp = date('Y-m-d H:i:s', strtotime($v));
						
						$exists = $wpdb->get_results(
							$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $parent_db . "_" . $type_db . " 
								WHERE `buoy_id` = '%s'
								AND `timestamp` = '%s'
								AND `url` = '%s'", 
								$buoy_id,
								$timestamp,
								$url
							)
						);
						
						if($wpdb->num_rows === 0) {
							$wpdb->insert(
								$wpdb->prefix . $parent_db . "_" . $type_db, 
								array( 
									'buoy_id' => $buoy_id, 
									'timestamp' => $timestamp,
									'url' => $url
								), 
								array( 
									'%s', 
									'%s',
									'%s'
								) 
							);
						}
					}
				}
			}
		}
	}
	
	function uwa_grab_and_fill_csv($bucket, $type, $parent_db, $type_db, $date_sep) {
		global $wpdb;
		 
		$is_datawell = ($parent_db == "datawell") ? true : false;
			
		$prefix = '';

		$buoys = $wpdb->get_results("SELECT * FROM `wp_buoy_info` WHERE `buoy_type` = '" . $parent_db . "'");
		// if($is_datawell) {
		uwa_log($parent_db, "Buoys found " . $wpdb->num_rows);
		// }

		foreach($buoys as $buoy) {
			$buoy_id = $buoy->buoy_id;
			$label = $buoy->aws_label;
			
			$prefix = $bucket . '/' . $buoy_id . '/' . date('Y') . '/'; // This date('Y') won't always be accurate
			
			$query = array(
				'buoy_type=' . $parent_db,
				'action=uwa_datawell_aws', // Global AWS Fetch
				'do=fetch_after_prefix', 
				'max-keys=48',
				'prefix=' . $prefix
			);
			
			// Needs to regrab other articles from current month 'LIMIT 11, 1' - the 11th last item
			$previous = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM " . $wpdb->prefix . $parent_db . "_" . $type_db . " 
					WHERE buoy_id = '%s' ORDER BY timestamp DESC LIMIT 11,1",
					$buoy_id
				)
			);
			if($wpdb->num_rows !== 0) {
				$query[] = 'previous=' . $previous->url;
			}

			// grab files from aws
			$json = uwa_aws_direct(uwa_query_array_to_key_value($query), true);

			$files = json_decode($json['html']);

			// number of files found
			uwa_log($parent_db, "Files found " . sizeof($files));
		
			// if there are files process them
			if(!empty($files)) {
				foreach($files as $file_a) {
					$url = $file_a[0];
					$date = $file_a[1];
					uwa_log($parent_db, "Processing " . $url);
					if(strpos($url, '.csv') > 0 || strpos($url, '.jpg') > 0) {
						uwa_log($parent_db, "Is CSV or JPG");
						$timestamp = str_replace('+00:00', '', $date);
						$timestamp = str_replace('T', ' ', $timestamp);
											
						$exists = $wpdb->get_results(
							$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $parent_db . "_" . $type_db . " 
								WHERE `buoy_id` = '%s'
								AND `timestamp` = '%s'
								AND `url` = '%s'", 
								$buoy_id,
								$timestamp,
								$url
							)
						);

						if($wpdb->num_rows === 0) {
							uwa_log($parent_db, "New file: " . $url); 
						}
						else {
							uwa_log($parent_db, "File exists: " . $url);
						}
					
						if($wpdb->num_rows === 0) {
							$wpdb->insert(
								$wpdb->prefix . $parent_db . "_" . $type_db, 
								array( 
									'buoy_id' => $buoy_id, 
									'timestamp' => $timestamp,
									'url' => $url
								), 
								array( 
									'%s', 
									'%s',
									'%s'
								) 
							);
						}
					}
					else {
						uwa_log($parent_db, "Not CSV or JPG");
					}
				}
			}
			else {
				uwa_log($parent_db, "Empty files");
			}
		}
	}
	
	function uwa_grab_and_fill_other() {
		print 'hello';
	}
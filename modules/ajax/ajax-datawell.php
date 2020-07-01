<?php
	// var_dump(function_exists('GuzzleHttp\uri_template'));
	// var_dump(function_exists('S3Client'));
	// print 1; die();

	require_once(UWA__PLUGIN_DIR . 'libraries/aws/aws-autoloader.php'); // 'libraries/aws.phar'); // 
				
	use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;

	function uwa_aws_direct($args, $return = false) {
		$header = [];
		$html = '';
		$log = '';

		uwa_log($args['buoy_type'], 'Preparing AWS fetch');

		if(isset($args['do'])) {
			$uwa_datawell_s3_key = get_option('uwa_datawell_s3_key');
			$uwa_datawell_s3_secret = get_option('uwa_datawell_s3_secret');
			$uwa_datawell_s3_region = get_option('uwa_datawell_s3_region');
			$uwa_datawell_s3_bucket = get_option('uwa_datawell_s3_bucket');

			uwa_log($args['buoy_type'], '... Fetching S3 credentials');
			
			if($uwa_datawell_s3_key && $uwa_datawell_s3_secret && $uwa_datawell_s3_region && $uwa_datawell_s3_bucket) {
				uwa_log($args['buoy_type'], '... Intiating S3 client');
				
				// Instantiate the client.
				$s3 = new S3Client([
					'credentials' => array(
						'key' => $uwa_datawell_s3_key,
						'secret' => $uwa_datawell_s3_secret
					),
					'version' => 'latest',
					'region'	=> $uwa_datawell_s3_region
				]);
				
				switch($args['do']) {
					//
					// Fetch All Files
					//
					case 'fetch_all':
						uwa_log($args['buoy_type'], '... Action "Fetch All"');
						$files = [];
						
						// Use the high-level iterators (returns ALL of your objects).
						try {
							$objects = $s3->getPaginator('ListObjects', [
								'Bucket' => $uwa_datawell_s3_bucket
							]);
						
							foreach ($objects as $object) {
								foreach ($object['Contents'] as $contents) {
									array_push($files, array($contents['Key'], $contents['LastModified']->__toString()));
								}
							}
						
						} catch (S3Exception $e) {
							$html = $e->getMessage() . PHP_EOL;
						}

						$html = json_encode($files);
						break;
					// 
					// Fetch After (New files only)
					//
					case 'fetch_after':
						uwa_log($args['buoy_type'], '... Action "Fetch After"');
						$start_after = $args['previous'];
						$max_keys = isset($args['max-keys']) ? $args['max-keys'] : 1000;
						
						$files = [];
						
						try {
							$objects = $s3->listObjectsV2([
								'Bucket' => $uwa_datawell_s3_bucket,
								'MaxKeys' => $max_keys,
								'StartAfter' => $start_after
							]);
							
							foreach ($objects['Contents'] as $object) {
								array_push($files, array($object['Key'], $object['LastModified']->__toString()));
							}
						
						} catch (S3Exception $e) {
							$html = $e->getMessage() . PHP_EOL;
						}

						$html = json_encode($files);
						break;
					// 
					// Fetch After with Prefix (New files only)
					//
					case 'fetch_after_prefix':
						uwa_log($args['buoy_type'], '... Action "Fetch After Prefix"');
						// $start_after = $args['previous'];
						$max_keys = isset($args['max-keys']) ? $args['max-keys'] : 1000; // Max limit 1000
						$prefix = $args['prefix'];
						$items = array(
							'Bucket' => $uwa_datawell_s3_bucket,
							'MaxKeys' => $max_keys,
							'Prefix' => $prefix
						);
						
						if(isset($args['previous'])) {
							$items['StartAfter'] = $args['previous'];
						}
						
						$files = [];

						uwa_log($args['buoy_type'], '... Items => ' . print_r($items, true));
						
						try {
							$objects = $s3->listObjectsV2($items);
							uwa_log($args['buoy_type'], '... Query success');
							// uwa_log($args['buoy_type'], '... Objects output: &#13;&#10;' . print_r($objects, true));
							if(isset($objects['Contents'])) {
								foreach ($objects['Contents'] as $object) {
									uwa_log($args['buoy_type'], '... Object found: ' . $object['Key'], $object['LastModified']->__toString());
									array_push($files, array($object['Key'], $object['LastModified']->__toString()));
								}
							}
							else {
								uwa_log($args['buoy_type'], '... Objects[content] is not set');
							}
						
						} catch (S3Exception $e) {
							$html = $e->getMessage() . PHP_EOL;
						}

						uwa_log($args['buoy_type'], '... Finished fetch');

						$html = json_encode($files);
						break;
					//
					// Fetch Single CSV
					//
					case 'csv_fetch':
						uwa_log($args['buoy_type'], '... Action "CSV Fetch"');
						uwa_log($args['buoy_type'], '... Args: ' . print_r($args, true));
						if(isset($args['key'])) {
							$keyname = $args['key'];
							uwa_log($args['buoy_type'], '... Keyname: ' . $keyname);
							try {
								// Get the object.
								$result = $s3->getObject([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);

								uwa_log($args['buoy_type'], '... Bucket => ' . $uwa_datawell_s3_bucket);
								// uwa_log($args['buoy_type'], '... AWS Result: ' . print_r($result['Body'], true));
							
								// Display the object in the browser.
								// header("Content-Type:application/csv"); 
								// header("Content-Disposition:attachment;filename=datawell-csv.csv"); 
								$header[] = "Content-Type:application/csv";
								$header[] = "Content-Disposition:attachment;filename=datawell-csv.csv";
								$html = $result['Body'];
							} catch (S3Exception $e) {
								$html = $e->getMessage() . PHP_EOL;
							}
						}
						break;
					//
					// Download CSV
					//
					case 'csv_download':
						uwa_log($args['buoy_type'], '... Action "CSV Download"');
						if(isset($args['key'])) {
							$keyname = $args['key'];
							
							// Ensure File Exists
							try {
								// Get the object.
								$result = $s3->getObjectTagging([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);

								// Fetch file
								try {
									// Get the object.
									$result = $s3->getObject([
										'Bucket' => $uwa_datawell_s3_bucket,
										'Key'		=> $keyname
									]);
							
									// Display the object in the browser.
									// header("Content-Type:application/csv"); 
									// header("Content-Disposition:attachment;filename=datawell-csv.csv"); 
									$header[] = "Content-Type:application/csv";
									$header[] = "Content-Disposition:attachment;filename=datawell-csv.csv";
									$html = $result['Body'];
								} catch (S3Exception $e) {
									$html = $e->getMessage() . PHP_EOL;
								}
							} catch (S3Exception $e) {
								// File doesn't exist
								$html = '';	
							}
						}
						break;
					// 
					// CSV Exists
					//
					case 'csv_exists':
						uwa_log($args['buoy_type'], '... Action "CSV Exists"');
						if(isset($args['key']) && isset($args['id'])) {
							$keyname = $args['key'];
							$id = $args['id'];
					
							try {
								// Get the object.
								$result = $s3->getObjectTagging([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);

								$html = $id;
							} catch (S3Exception $e) {
								$html = -1;
							}
						}
						else {
							$html = -1;
						}
						break;
					//
					// Get Image
					//
					case 'image_fetch':
						uwa_log($args['buoy_type'], '... Action "Image Fetch"');
						if(isset($args['key'])) {
							$keyname = $args['key'];
							try {
								// Get the object.
								$result = $s3->getObject([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);
						
								// Display the object in the browser.
								// header("Content-Type: {$result['ContentType']}");
								$header[] = "Content-Type: {$result['ContentType']}";
								$html = $result['Body'];

							} catch (S3Exception $e) {
								$html = $e->getMessage() . PHP_EOL;
							}
						}
						break;
					//
					// Get List of 1D + 2D CSVs
					//
					// Depricated ---
					// REMOVE
					///
					case 'spectrum_csvs':
						uwa_log($args['buoy_type'], '... Action "Spectrum CSVs"');
						if(isset($args['buoy_id']) && isset($args['dates']) && isset($args['spectrum'])) { // && isset($args['csv-type'])
							global $wpdb;
							
							$database = '_spec_' . $args['spectrum'];

							$dates = urldecode($args['dates']);
							$dates = explode(' - ', $dates);
							
							if(sizeof($dates) == 2) {
								$from_exploded = explode('/', $dates[0]);
								$from = date('Y-m-d 00:00:00', strtotime($from_exploded[1] . '/' . $from_exploded[0] . '/' . $from_exploded[2]));
								$until_exploded = explode('/', $dates[1]);
								$until = date('Y-m-d 23:59:59', strtotime($until_exploded[1] . '/' . $until_exploded[0] . '/' . $until_exploded[2]));
								$date_set = true;
								
								// Get CSV List
								$buoy_id = isset($args['buoy_id']) ? $args['buoy_id'] : 'Dev_site';
	
								$memplot = $wpdb->get_results(
									$wpdb->prepare(
										"SELECT * FROM " . $wpdb->prefix . "datawell" . $database . " 
										WHERE `timestamp` >= '%s'
										AND `timestamp` <= '%s'
										AND `buoy_id` = '%s' 
										ORDER BY `timestamp` ASC",
										$from,
										$until,
										$buoy_id
									)
								);				
								
								if($memplot) {
									$file_list = array();
									foreach($memplot as $m) {
										$file_list[] = $m->url;
									}
									
									$html = json_encode($file_list);
								}
								else {
									$html = 0;
								}
							}
							else {
								$html = 'Incorrect Date Format';
							}
						}
						
						break;
					default: 
						break;
				}
			}
			else {
				uwa_log($args['buoy_type'], '...S3 credentials are not set');
			}
		}
		else {
			uwa_log($args['buoy_type'], 'No "do" argument set');
		}

		if($return) {
			return array('header' => $header, 'html' => $html);
		}
		// Print Header and HTML
		foreach($header as $head) {
			header($head);
		}
		print $html;
	}

	// datawell version replaced by global above
	function uwa_datawell_aws_direct($args, $return = false) {
		$args['buoy_type'] = 'datawell';
		uwa_aws_direct($args, $return);
	}
	

	function uwa_datawell_aws() {
		$args = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

		uwa_datawell_aws_direct($args);
		
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
		$files = uwa_datawell_fetch_s3_file_list(true);
		
		// Process CSVs
		uwa_datawell_process_csvs();
		
		// Process Images
		// uwa_datawell_process_jpgs($files);
		
		print 1;
		
		wp_die();
 	}
 	
 	add_action( 'wp_ajax_update_datawell', 'cron_update_datawell' );
	add_action( 'wp_ajax_nopriv_update_datawell', 'cron_update_datawell' );
	
	// Cron Second Memplot, 1d and 2d
 	function cron_rev_update_datawell() {
		uwa_datawell_grab_and_fill('Spec1D', 'spec_1d', '_Spec1D_');
		
		uwa_datawell_grab_and_fill('Spec2D', 'spec_2d', '_Spec2D_');
		
		uwa_datawell_grab_and_fill('MEMplot', 'memplot', '_MEMplot_');

		print 1;
		
		wp_die();
 	}
	
	add_action( 'wp_ajax_update_datawell_rev', 'cron_rev_update_datawell' );
	add_action( 'wp_ajax_nopriv_update_datawell_rev', 'cron_rev_update_datawell' );
	
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
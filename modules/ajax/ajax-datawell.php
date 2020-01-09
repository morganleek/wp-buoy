<?php
	require_once(UWA__PLUGIN_DIR . 'libraries/aws/aws-autoloader.php'); // 'libraries/aws.phar'); // 
				
	use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;

	function uwa_datawell_aws() {
		
		if(isset($_GET['do'])) {
			$uwa_datawell_s3_key = get_option('uwa_datawell_s3_key');
			$uwa_datawell_s3_secret = get_option('uwa_datawell_s3_secret');
			$uwa_datawell_s3_region = get_option('uwa_datawell_s3_region');
			$uwa_datawell_s3_bucket = get_option('uwa_datawell_s3_bucket');
			
			if($uwa_datawell_s3_key && $uwa_datawell_s3_secret && $uwa_datawell_s3_region && $uwa_datawell_s3_bucket) {
				// Instantiate the client.
				$s3 = new S3Client([
					'credentials' => array(
						'key' => $uwa_datawell_s3_key,
						'secret' => $uwa_datawell_s3_secret
					),
					'version' => 'latest',
					'region'	=> $uwa_datawell_s3_region
				]);
				
				switch($_GET['do']) {
					//
					// Fetch All Files
					//
					case 'fetch_all':
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
							echo $e->getMessage() . PHP_EOL;
						}

						print json_encode($files);
						break;
					// 
					// Fetch After (New files only)
					//
					case 'fetch_after':
						$start_after = $_GET['previous'];
						$max_keys = isset($_GET['max-keys']) ? $_GET['max-keys'] : 1000;
						
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
							echo $e->getMessage() . PHP_EOL;
						}

						print json_encode($files);
						break;
					// 
					// Fetch After with Prefix (New files only)
					//
					case 'fetch_after_prefix':
						// $start_after = $_GET['previous'];
						$max_keys = isset($_GET['max-keys']) ? $_GET['max-keys'] : 1000; // Max limit 1000
						$prefix = $_GET['prefix'];
						$items = array(
							'Bucket' => $uwa_datawell_s3_bucket,
							'MaxKeys' => $max_keys,
							'Prefix' => $prefix
						);
						
						if(isset($_GET['previous'])) {
							$items['StartAfter'] = $_GET['previous'];
						}
						
						$files = [];
						
						try {
							$objects = $s3->listObjectsV2($items);
							
							foreach ($objects['Contents'] as $object) {
								array_push($files, array($object['Key'], $object['LastModified']->__toString()));
							}
						
						} catch (S3Exception $e) {
							echo $e->getMessage() . PHP_EOL;
						}

						print json_encode($files);
						break;
					//
					// Fetch Single CSV
					//
					case 'csv_fetch':
						if(isset($_GET['key'])) {
							$keyname = $_GET['key'];
							try {
								// Get the object.
								$result = $s3->getObject([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);
							
								// Display the object in the browser.
								header("Content-Type:application/csv"); 
								header("Content-Disposition:attachment;filename=datawell-csv.csv"); 
								print $result['Body'];
							} catch (S3Exception $e) {
								print $e->getMessage() . PHP_EOL;
							}
						}
						break;
					//
					// Download CSV
					//
					case 'csv_download':
						if(isset($_GET['key'])) {
							$keyname = $_GET['key'];
							
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
									header("Content-Type:application/csv"); 
									header("Content-Disposition:attachment;filename=datawell-csv.csv"); 
									print $result['Body'];
								} catch (S3Exception $e) {
									print $e->getMessage() . PHP_EOL;
								}
							} catch (S3Exception $e) {
								// File doesn't exist
								print '';	
							}
						}
						break;
					// 
					// CSV Exists
					//
					case 'csv_exists':
						if(isset($_GET['key']) && isset($_GET['id'])) {
							$keyname = $_GET['key'];
							$id = $_GET['id'];
					
							try {
								// Get the object.
								$result = $s3->getObjectTagging([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);

								print $id;
							} catch (S3Exception $e) {
								print -1;
							}
						}
						else {
							print -1;
						}
						break;
					//
					// Get Image
					//
					case 'image_fetch':
						if(isset($_GET['key'])) {
							$keyname = $_GET['key'];
							try {
								// Get the object.
								$result = $s3->getObject([
									'Bucket' => $uwa_datawell_s3_bucket,
									'Key'		=> $keyname
								]);
						
								// Display the object in the browser.
								header("Content-Type: {$result['ContentType']}");
								print $result['Body'];

							} catch (S3Exception $e) {
								print $e->getMessage() . PHP_EOL;
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
					
						if(isset($_GET['buoy_id']) && isset($_GET['dates']) && isset($_GET['spectrum'])) { // && isset($_GET['csv-type'])
							global $wpdb;
							
							$database = '_spec_' . $_GET['spectrum'];

							$dates = urldecode($_GET['dates']);
							$dates = explode(' - ', $dates);
							
							if(sizeof($dates) == 2) {
								$from_exploded = explode('/', $dates[0]);
								$from = date('Y-m-d 00:00:00', strtotime($from_exploded[1] . '/' . $from_exploded[0] . '/' . $from_exploded[2]));
								$until_exploded = explode('/', $dates[1]);
								$until = date('Y-m-d 23:59:59', strtotime($until_exploded[1] . '/' . $until_exploded[0] . '/' . $until_exploded[2]));
								$date_set = true;
								
								// Get CSV List
								$buoy_id = isset($_GET['buoy_id']) ? $_GET['buoy_id'] : 'Dev_site';
	
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
									
									print json_encode($file_list);
								}
								else {
									print 0;
								}
							}
							else {
								print 'Incorrect Date Format';
							}
						}
						
						break;
					default: 
						break;
				}
			}
		}
		
		wp_die();
	}
	
	add_action( 'wp_ajax_uwa_datawell_aws', 'uwa_datawell_aws' );
	add_action( 'wp_ajax_nopriv_uwa_datawell_aws', 'uwa_datawell_aws' );
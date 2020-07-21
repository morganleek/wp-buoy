<?php 
	/*
	Plugin Name:  WP Buoy
	Plugin URI:   https://morganleek.me/
	Description:  Buoy Monitoring Plugin
	Version:      20200109
	Author:       https://morganleek.me/
	Author URI:   https://morganleek.me/
	License:      GPL2
	License URI:  https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain:  wporg
	Domain Path:  /languages
	*/

	// Security
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	// Version
	define( 'UWA_VERSION', '20200112' );
	// Paths
	define( 'UWA__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'UWA__PLUGIN_URL', plugin_dir_url( __FILE__ ) );

	// Options
	global $uwa_db_version;
	$uwa_db_version = '2.0.11';
	
	global $wpdb;
	
	// These need to be combined
	global $uwa_photo_lookup, $uwa_buoy_details; 
	
	$buoy_info = $wpdb->get_results("SELECT * FROM wp_buoy_info");
	
	foreach($buoy_info as $b) {

		$uwa_photo_lookup[$b->buoy_id] = $b->image_url;
		$uwa_buoy_details[$b->buoy_id] = array(
			'title' => $b->title,
			'aws_label' => $b->aws_label,
			'description' => $b->description, 
			'depth' => ($b->depth > 0) ? $b->depth . 'm' : '',
			'image_url' => $b->image_url,
			'visible' => $b->visible,
			'hide_location' => $b->hide_location,
			'buoy_type' => $b->buoy_type,
			'custom_lat' => $b->custom_lat,
			'custom_lng' => $b->custom_lng
		);
	}
	
	if(!class_exists('WP_List_Table')) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	
	// Library
	require_once( UWA__PLUGIN_DIR . 'libraries/tools.php' );
	
	require_once( UWA__PLUGIN_DIR . 'admin/construct.php' );
	require_once( UWA__PLUGIN_DIR . 'admin/buoy-info.php' );
	require_once( UWA__PLUGIN_DIR . 'admin/menus.php' );
	require_once( UWA__PLUGIN_DIR . 'admin/general.php' );
	
	// Different Buoy Modules 
	require_once( UWA__PLUGIN_DIR . 'modules/views/view-global.php' );
	require_once( UWA__PLUGIN_DIR . 'modules/views/view-charts.php' );
	require_once( UWA__PLUGIN_DIR . 'modules/spoondrift.php' );
	require_once( UWA__PLUGIN_DIR . 'modules/datawell.php' );
	require_once( UWA__PLUGIN_DIR . 'modules/triaxy.php' );

	// API
	require_once( UWA__PLUGIN_DIR . 'api/api.php' );
	

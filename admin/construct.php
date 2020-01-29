<?php
	// Install
	function uwa_install() {
		global $wpdb;
		global $uwa_db_version;

		// Create Database Tables
		$charset_collate = $wpdb->get_charset_collate();
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Spoondrift
		$table_name = $wpdb->prefix . "spoondrift_post_data";
		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		  data text NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "spoondrift_post_data_processed";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_data_id mediumint(9) NOT NULL,
			spotter_id varchar(100) NOT NULL,
			spotter_name varchar(100) NOT NULL,
			payload_type varchar(20) NOT NULL,
			battery_voltage DECIMAL(5,2) NOT NULL,
			solar_voltage DECIMAL(5,2) NOT NULL,
			temperature DECIMAL(5,2) NOT NULL,
			humidity DECIMAL(5,2) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "spoondrift_post_data_processed_waves";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_data_processed_id mediumint(9) NOT NULL,
			significant_wave_height DECIMAL(5,2) NOT NULL,
			peak_period DECIMAL(5,2) NOT NULL,
			mean_period DECIMAL(5,2) NOT NULL,
			peak_direction DECIMAL(5,2) NOT NULL,
			peak_directional_spread DECIMAL(5,2) NOT NULL,
			mean_direction DECIMAL(5,2) NOT NULL,
			mean_directional_spread DECIMAL(5,2) NOT NULL,
			timestamp datetime NOT NULL,
			latitude DECIMAL(10, 8) NOT NULL,
			longitude DECIMAL(11, 8) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "spoondrift_post_data_processed_wind";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			spoondrift_lookup_id mediumint(9) NOT NULL,
			speed DECIMAL(5,2) NOT NULL,
			direction DECIMAL(5,2) NOT NULL,
			surface_id TINYINT(1) DEFAULT 0,
			timestamp datetime NOT NULL,
			latitude DECIMAL(10, 8) NOT NULL,
			longitude DECIMAL(11, 8) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		// Datawell Files
		$table_name = $wpdb->prefix . "datawell_file_data";
		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		  last_modified datetime NOT NULL,
		  url VARCHAR (255) NOT NULL, 
		  title VARCHAR (50) NOT NULL,
		  year VARCHAR (10) NOT NULL,
		  month VARCHAR (10) NOT NULL,
		  file_name VARCHAR (100) NOT NULL,
		  processed TINYINT (1) NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "datawell_post_data_processed_waves";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			significant_wave_height DECIMAL(5,2) DEFAULT 0,
			max_wave_height DECIMAL(5,2) DEFAULT 0,
			peak_period DECIMAL(5,2) DEFAULT 0,
			mean_period DECIMAL(5,2) DEFAULT 0,
			peak_direction DECIMAL(5,2) DEFAULT 0,
			peak_directional_spread DECIMAL(5,2) DEFAULT 0,
			mean_direction DECIMAL(5,2) DEFAULT 0,
			mean_directional_spread DECIMAL(5,2) DEFAULT 0,
			timestamp datetime NOT NULL,
			latitude DECIMAL(10, 8) DEFAULT 0,
			longitude DECIMAL(11, 8) DEFAULT 0,
			memplot VARCHAR(255),
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		// Datawell Spec 1D + 2D
		$table_name = $wpdb->prefix . "datawell_lookup";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			buoy_label varchar(100) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "datawell_spec_1d";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "datawell_spec_2d";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "datawell_memplot";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "datawell_csv";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		// Spoondrift Spec 1D + 2D
		$table_name = $wpdb->prefix . "spoondrift_lookup";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			buoy_label varchar(100) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "spoondrift_spec_1d";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "spoondrift_spec_2d";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "spoondrift_memplot";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			url VARCHAR (255) NOT NULL, 
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		// General
		$table_name = $wpdb->prefix . "buoy_info";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_id varchar(100) NOT NULL,
			aws_label varchar(100) NOT NULL,
			title varchar(100) NOT NULL,
			description TEXT NOT NULL,
			depth varchar(100) NOT NULL,
			buoy_type varchar(100) NOT NULL,
			visible TINYINT(1) DEFAULT 0,
			hide_location TINYINT(1) DEFAULT 0,
			custom_lat varchar(50) NOT NULL,
			custom_lng varchar(50) NOT NULL,
			image_url VARCHAR(255) NOT NULL,
			homepage_graph_event_limit SMALLINT DEFAULT 0,
			wave_height_increments VARCHAR(255),
			buoy_order SMALLINT DEFAULT 0,
			true_north_offset DECIMAL(5,2) NOT NULL,
			spotter_token varchar(100) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql );

		// Triaxy
		$table_name = $wpdb->prefix . "triaxy_serial_lookup";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_serial varchar(100) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql );
		
		$table_name = $wpdb->prefix . "triaxy_ftp_folder";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			file_path varchar(255) NOT NULL,
			total_files mediumint(9) NOT NULL,
			timestamp datetime NOT NULL,
			complete TINYINT (1) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "triaxy_ftp_wave_files";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_serial varchar(100) NOT NULL,
			timestamp datetime NOT NULL,
			size mediumint(9) NOT NULL,
			file_path varchar(255) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql );

		$table_name = $wpdb->prefix . "triaxy_post_data_processed_waves";
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			buoy_serial_id mediumint(9) NOT NULL,
			number_of_zero_crossings mediumint(9) NOT NULL,
			average_wave_height DECIMAL(5,2) NOT NULL,
			t_avg DECIMAL(5,2) NOT NULL,
			max_wave_height DECIMAL(5,2) NOT NULL,
			t_max DECIMAL(5,2) NOT NULL,
			significant_wave_height DECIMAL(5,2) NOT NULL,
			significant_wave_peroid DECIMAL(5,2) NOT NULL,
			h10 DECIMAL(5,2) NOT NULL,
			t10 DECIMAL(5,2) NOT NULL,
			peak_crest DECIMAL(5,2) NOT NULL,
			mean_period DECIMAL(5,2) NOT NULL,
			peak_period DECIMAL(5,2) NOT NULL,
			peak_direction DECIMAL(5,2) NOT NULL,
			peak_directional_spread DECIMAL(5,2) NOT NULL,
			tp5 DECIMAL(5,2) NOT NULL,
			hm0 DECIMAL(5,2) NOT NULL,
			mean_direction DECIMAL(5,2) NOT NULL, 
			mean_directional_spread DECIMAL(5,2) NOT NULL, 
			te DECIMAL(5,2) NOT NULL, 
			wave_steepness DECIMAL(5,3) NOT NULL,
			timestamp datetime NOT NULL,
			latitude DECIMAL(10, 8) NOT NULL,
			longitude DECIMAL(11, 8) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta( $sql );

		update_option( 'uwa_db_version', $uwa_db_version );
		
		add_action( 'admin_notices', function() { print '<div class="notice notice-success is-dismissible"><p>UWA Buoy DB Updated</p></div>'; } );
		// Rewrites
		// uwa_rewrite_add_rewrites();
		// flush_rewrite_rules();
	}
	register_activation_hook( __FILE__, 'uwa_install' );

	function uwa_update_db_check() {
    global $uwa_db_version;
    if ( get_site_option( 'uwa_db_version' ) != $uwa_db_version ) {
      uwa_install();
    }
	}
	add_action( 'plugins_loaded', 'uwa_update_db_check' );
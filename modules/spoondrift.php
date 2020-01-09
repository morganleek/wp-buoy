<?php

 	// Front end view functions
 	require_once( UWA__PLUGIN_DIR . 'modules/views/view-spoondrift.php' );
 	// Ajax
	require_once( UWA__PLUGIN_DIR . 'modules/ajax/ajax-spoondrift.php' );
	
	function uwa_spoondrift_grab_and_fill($type, $type_db, $date_sep) {
		uwa_grab_and_fill('SpoondriftBuoys', $type, 'spoondrift', $type_db, $date_sep);
 	}
 	
 	/* 
 	** AJAX
 	*/ 
 	
	// Cron Second Memplot, 1d and 2d
 	function cron_rev_update_spoondrift() {
		uwa_spoondrift_grab_and_fill('Spec1D', 'spec_1d', '_Spec1D_');
		
		uwa_spoondrift_grab_and_fill('Spec2D', 'spec_2d', '_Spec2D_');
		
		uwa_spoondrift_grab_and_fill('MEMplot', 'memplot', '_MEMplot_');
		
		print 1;
		
		wp_die();
 	}
	
	add_action( 'wp_ajax_update_spoondrift_rev', 'cron_rev_update_spoondrift' );
	add_action( 'wp_ajax_nopriv_update_spoondrift_rev', 'cron_rev_update_spoondrift' );

	//
  // Table Layout
  //
  class Spoondrift_List extends WP_List_Table {

		// Class constructor 
		public function __construct() {
	
			parent::__construct( [
				'singular' => __( 'Spoondrift Event', 'sp' ), //singular name of the listed records
				'plural'   => __( 'Spoondrift Events', 'sp' ), //plural name of the listed records
				'ajax'     => false //should this table support ajax?
			]);
		}
		
		public static function get_spoondrift_events( $per_page = 50, $page_number = 1 ) {
		  global $wpdb;

		  // $sql = "SELECT *, 1 AS `processed` FROM {$wpdb->prefix}spoondrift_post_data";
		  $sql = "SELECT * FROM {$wpdb->prefix}spoondrift_post_data AS D
				LEFT JOIN {$wpdb->prefix}spoondrift_post_data_processed AS P
				ON D.`id` = P.`post_data_id`";
		
		  if ( ! empty( $_REQUEST['orderby'] ) ) {
		    $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		    $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		  }
		
		  $sql .= " LIMIT $per_page";
		
		  $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		
		  $result = $wpdb->get_results( $sql, 'ARRAY_A' );
		
		  return $result;
		}

		public static function delete_spoondrift_event( $id ) {
		  global $wpdb;
		
		  $wpdb->delete(
		    "{$wpdb->prefix}spoondrift_post_data",
		    [ 'ID' => $id ],
		    [ '%d' ]
		  );
		}
		
		public static function record_count() {
		  global $wpdb;
		
		  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}spoondrift_post_data";
		
		  return $wpdb->get_var( $sql );
		}
		
		// Text displayed when no customer data is available
		public function no_items() {
		  _e( 'No Spoondrift events avaliable.', 'sp' );
		}
		
		function column_name( $item ) {
		
		  // create a nonce
		  $delete_nonce = wp_create_nonce( 'sp_delete_spoondrift_event' );
		
		  $title = '<strong>' . $item['time'] . '</strong>';
		
		  $actions = [
		    'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		  ];
		
		  return $title . $this->row_actions( $actions );
		}
		
		public function column_default( $item, $column_name ) {
		  switch ( $column_name ) {
		    case 'time':
		    	return $item['time'];
		    case 'data':
		     	// return '<pre>' . print_r(json_decode($item['data']), true) . '</pre>';
		    	return substr($item['data'], 0, 100) . '&hellip;';
		    case 'processed':
		    	return ($item['post_data_id'] == '') ? 'FALSE' : 'TRUE';
		    default:
		    	return $column_name . ': No case';
		      // return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		  }
		}
		
		function column_cb( $item ) {
		  return sprintf(
		    '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		  );
		}
		
		function get_columns() {
		  $columns = [
		    'cb'      => '<input type="checkbox" />',
		    'time'    => __( 'Time', 'uwa' ),
		    'processed' => __( 'Processed', 'uwa' ),
		    'data' => __( 'Data', 'uwa' )
		  ];
		
		  return $columns;
		}
		
		public function get_sortable_columns() {
		  // $sortable_columns = array(
		  //   'name' => array( 'name', true ),
		  //   'city' => array( 'city', false )
		  // );
		
		  return array(); // $sortable_columns;
		}
		
		public function get_bulk_actions() {
		  $actions = [
		    'bulk-delete' => 'Delete'
		  ];
		
		  return $actions;
		}
		
		public function prepare_items() {
		
		  $this->_column_headers = $this->get_column_info();
		
		  // Process bulk action
		  $this->process_bulk_action();
		
		  $per_page     = $this->get_items_per_page( 'spoondrift_events_per_page', 20 );
		  $current_page = $this->get_pagenum();
		  $total_items  = self::record_count();
		
		  $this->set_pagination_args( [
		    'total_items' => $total_items, //WE have to calculate the total number of items
		    'per_page'    => $per_page //WE have to determine how many items to show on a page
		  ] );
		
		
		  $this->items = self::get_spoondrift_events( $per_page, $current_page );
		}
		
		public function process_bulk_action() {

		  //Detect when a bulk action is being triggered...
		  if ( 'delete' === $this->current_action() ) {
		
		    // In our file that handles the request, verify the nonce.
		    $nonce = esc_attr( $_REQUEST['_wpnonce'] );
		
		    if ( ! wp_verify_nonce( $nonce, 'sp_delete_spoondrift_event' ) ) {
		      die( 'Go get a life script kiddies' );
		    }
		    else {
		      self::delete_spoondrift_event( absint( $_GET['id'] ) );
		
		      wp_redirect( esc_url( add_query_arg() ) );
		      exit;
		    }
		
		  }
		
		  // If the delete bulk action is triggered
		  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		       || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		  ) {
		
		    $delete_ids = esc_sql( $_POST['bulk-delete'] );
		
		    // loop over the array of record IDs and delete them
		    foreach ( $delete_ids as $id ) {
		      self::delete_spoondrift_event( $id );
		
		    }
		
		    wp_redirect( esc_url( add_query_arg() ) );
		    exit;
		  }
		}	
	}
	
	
	class Spoondrift_Plugin {

		// class instance
		static $instance;
	
		// customer WP_List_Table object
		public $spoondrift_obj;
	
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
		    'Spoondrift Dashboard',
		    'Spoondrift',
		    'manage_options',
		    'uwa-spoondrift',
		    [$this, 'plugin_settings_page']
		  );
	
			add_action( "load-$hook", [ $this, 'screen_option' ] );
	
		}
	
	
		public function plugin_settings_page() {
			?>
			<div class="wrap">
				<h2>Spoondrift Events</h2>
	
				<div id="spoondrift-events">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post">
									<?php
									$this->spoondrift_obj->prepare_items();
									$this->spoondrift_obj->display(); ?>
								</form>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
			</div>
		<?php
		}
	
		public function screen_option() {
	
			$option = 'per_page';
			$args   = [
				'label'   => 'Spoondrift Events',
				'default' => 50,
				'option'  => 'spoondrift_events_per_page'
			];
	
			add_screen_option( $option, $args );
	
			$this->spoondrift_obj = new Spoondrift_List();
		}
	
	
		// Singleton instance
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
	
			return self::$instance;
		}
	}
	
	add_action( 'plugins_loaded', function () {
		Spoondrift_Plugin::get_instance();
	} );

	// Process Raw Data
	function uwa_process_spoondrift() {
		global $wpdb;

		$unprocessed = $wpdb->get_results("SELECT * 
			FROM `" . $wpdb->prefix . "spoondrift_post_data` 
			WHERE `id` NOT IN 
			(SELECT `post_data_id` AS id FROM `" . $wpdb->prefix . "spoondrift_post_data_processed`)
		");

		if($unprocessed) {
			foreach($unprocessed as $u) {
				$post_data_id = $u;
				$data = json_decode($u->data);

				$wpdb->insert(
					$wpdb->prefix . 'spoondrift_post_data_processed', 
					array( 
						'post_data_id' => $post_data_id->id,
						'spotter_id' => ($data->data->spotterId == null) ? '' : $data->data->spotterId,
						'spotter_name' => ($data->data->spotterName == null) ? '' : $data->data->spotterName,
						'payload_type' => ($data->data->payloadType == null) ? '' : $data->data->payloadType,
						'battery_voltage' => ($data->data->batterVoltage == null) ? 0 : $data->data->batterVoltage,
						'solar_voltage' => ($data->data->solarVoltage == null) ? 0 : $data->data->solarVoltage,
						'humidity' => ($data->data->humidity == null) ? 0 : $data->data->humidity
					), 
					array( 
						'%d',
						'%s',
						'%s',
						'%s',
						'%f',
						'%f',
						'%f'
					) 
				);

				$processed_id = $wpdb->insert_id;

				// Add Each Wave 
				foreach($data->data->waves as $w) {
					$wpdb->insert(
						$wpdb->prefix . 'spoondrift_post_data_processed_waves', 
						array( 
							'post_data_processed_id' => $processed_id,
							'significant_wave_height' => ($w->significantWaveHeight == null) ? 0 : $w->significantWaveHeight,
							'peak_period' => ($w->peakPeriod == null) ? 0 : $w->peakPeriod,
							'mean_period' => ($w->meanPeriod == null) ? 0 : $w->meanPeriod,
							'peak_direction' => ($w->peakDirection == null) ? 0 : $w->peakDirection,
							'peak_directional_spread' => ($w->peakDirectionalSpread == null) ? 0 : $w->peakDirectionalSpread,
							'mean_direction' => ($w->meanDirection == null) ? 0 : $w->meanDirection,
							'mean_directional_spread' => ($w->meanDirectionalSpread == null) ? 0 : $w->meanDirectionalSpread,
							'timestamp' => ($w->timestamp == null) ? '' : $w->timestamp,
							'latitude' => ($w->latitude == null) ? 0 : $w->latitude,
							'longitude' => ($w->longitude == null) ? 0 : $w->longitude,
						), 
						array( 
							'%f',
							'%f',
							'%f',
							'%f',
							'%f',
							'%f',
							'%f',
							'%f',
							'%s',
							'%f',
							'%f'
						) 
					);
				}
			}
		}
	}
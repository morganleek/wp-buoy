<?php
	if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Buoy_Info_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Buoy', 'uwa' ), //singular name of the listed records
			'plural'   => __( 'Buoys', 'uwa' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );
	}


	/**
	 * Retrieve buoys data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_buoys( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}buoy_info";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a buoy record.
	 *
	 * @param int $id buoy ID
	 */
	public static function delete_buoy( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}buoy_info",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}buoy_info";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no buoy data is available */
	public function no_items() {
		_e( 'No buoys avaliable.', 'uwa' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'buoy_id':
			case 'title':
				return $item[ $column_name ];
			case 'visible':
			case 'hide_location':
				return ($item[ $column_name ] == 1) ? 'Yes' : 'No';
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'uwa_delete_buoy' );

		$title = '<strong>' . $item['buoy_id'] . '</strong>';

		$actions = [
			'edit' => sprintf( '<a href="?page=%s&action=%s&buoy=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] )),
			'delete' => sprintf( '<a href="?page=%s&action=%s&buoy=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Buoy ID', 'uwa' ),
			'title' => __( 'Title', 'uwa' ),
			'visible'    => __( 'Visible', 'uwa' )
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			// 'name' => array( 'name', true ),
			// 'city' => array( 'city', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'buoys_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_buoys( $per_page, $current_page );
	}

	public function process_bulk_action() {
		
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			
			if ( ! wp_verify_nonce( $nonce, 'uwa_delete_buoy' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_buoy( absint( $_GET['buoy'] ) );
			}
		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_buoy( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}

}


class Buoy_Info_Plugin {

	// class instance
	static $instance;

	// Buoy WP_List_Table object
	public $buoys_obj;

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
	    'Buoys Info',
	    'manage_options',
	    'uwa-buoy-info',
	    [$this, 'plugin_settings_page']
	  );

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>Buoys Info</h2>
			<div id="col-container" class="wp-clearfix">
				<div id="col-left">
					<div id="col-wrap">
						<div class="form-wrap">
							<?php
								global $wpdb;
								
								$title = 'Add New Buoy';
								
								if(isset($_POST['buoy-info'])) {
									// nonce check
									
									
									// Existing
									if(isset($_POST['hidden-id']) && !empty($_POST['hidden-id'])) {
										$wpdb->query(
											$wpdb->prepare("
												UPDATE {$wpdb->prefix}buoy_info
												SET `buoy_id` = '%s',
												`aws_label` = '%s',
												`title` = '%s',
												`description` = '%s',
												`buoy_type` = '%s',
												`visible` = %d,
												`hide_location` = %d,
												`custom_lat` = '%s',
												`custom_lng` = '%s',
												`image_url` = '%s',
												`homepage_graph_event_limit` = '%s',
												`depth` = '%s',
												`wave_height_increments` = '%s'
												WHERE `id` = %d
											", 
												$_POST['tag-buoy-id'], 
												$_POST['tag-aws-label'],
												$_POST['tag-title'],
												$_POST['tag-description'],
												$_POST['tag-buoy-type'],
												isset($_POST['tag-enabled']) ? 1 : 0,
												isset($_POST['tag-hide-location']) ? 1 : 0,
												isset($_POST['tag-custom-lat']) ? $_POST['tag-custom-lat'] : '',
												isset($_POST['tag-custom-lng']) ? $_POST['tag-custom-lng'] : '',
												$_POST['tag-image'],
												$_POST['tag-intervals'],
												$_POST['tag-depth'],
												$_POST['tag-wave-height-increments'],
												$_POST['hidden-id']
											)
										);
									}	
									// New
									else {
										$wpdb->insert( 
											"{$wpdb->prefix}buoy_info", 
											array( 
												'buoy_id' => $_POST['tag-buoy-id'], 
												'aws_label' => $_POST['tag-aws-label'],
												'title' => $_POST['tag-title'],
												'description' => $_POST['tag-description'],
												'buoy_type' => $_POST['tag-buoy-type'],
												'visible' => isset($_POST['tag-enabled']) ? 1 : 0,
												'hide_location' => isset($_POST['tag-hide-location']) ? 1 : 0,
												'custom_lat' => isset($_POST['tag-custom-lat']) ? $_POST['tag-custom-lat'] : '', 
												'custom_lng' => isset($_POST['tag-custom-lng']) ? $_POST['tag-custom-lng'] : '', 
												'image_url' => $_POST['tag-image'], 
												'homepage_graph_event_limit' => $_POST['tag-intervals'],
												'depth' => $_POST['tag-depth'],
												'wave_height_increments' => $_POST['tag-wave-height-increments']
											), 
											array( 
												'%s', 
												'%s',
												'%s', 
												'%s',
												'%s',
												'%d',
												'%d',
												'%s', 
												'%s', 
												'%s', 
												'%s', 
												'%d',
												'%s'
											) 
										);
										
										// _d($wpbd);
									}
									
								}
								else if(isset($_REQUEST['action'])) {
									
									if($_REQUEST['action'] === 'edit' && $_REQUEST['buoy']) {
										
										$buoy = $wpdb->get_row(
											$wpdb->prepare("SELECT * FROM {$wpdb->prefix}buoy_info WHERE id = %d", $_REQUEST['buoy'])
										);
										
										$form_data = array(
											'id' => $buoy->id,
											'buoy_id' => $buoy->buoy_id,
											'aws_label' => $buoy->aws_label,
											'title' => $buoy->title,
											'description' => $buoy->description,
											'buoy_type' => $buoy->buoy_type,
											'visible' => $buoy->visible,
											'hide_location' => $buoy->hide_location,
											'custom_lat' => $buoy->custom_lat,
											'custom_lng' => $buoy->custom_lng,
											'image_url' => $buoy->image_url,
											'homepage_graph_event_limit' => $buoy->homepage_graph_event_limit,
											'depth' => $buoy->depth,
											'wave_height_increments' => $buoy->wave_height_increments
										);		
										
										$title = 'Edit Existing Buoy';
									}
								}
								
								// Form
							?>
							<h2><?php print $title; ?></h2>
							<form method="post">
								<input type="hidden" name="buoy-info" value="1">
								<input type="hidden" name="hidden-id" value="<?php print (isset($form_data['id'])) ? $form_data['id'] : ''; ?>"> 
								<div class="form-field form-required term-buoy-id-wrap">
									<label for="tag-buoy-id">Buoy ID</label>
									<input name="tag-buoy-id" id="tag-buoy-id" type="text" value="<?php print (isset($form_data['buoy_id'])) ? $form_data['buoy_id'] : ''; ?>" size="40" aria-required="true">
									<p>ID Used by Buoy Provider</p>
								</div>
								<div class="form-field form-required term-buoy-aws-label-wrap">
									<label for="tag-aws-label">AWS Label</label>
									<input name="tag-aws-label" id="tag-aws-label" type="text" value="<?php print (isset($form_data['aws_label'])) ? $form_data['aws_label'] : ''; ?>" size="40" aria-required="true">
									<p>Root folder of AWS files (I.e. TorbayInshore)</p>
								</div>
								<div class="form-field form-required term-title-wrap">
									<label for="tag-title">Title</label>
									<input name="tag-title" id="tag-title" type="text" value="<?php print (isset($form_data['title'])) ? $form_data['title'] : ''; ?>" size="40" aria-required="true">
									<!-- <p></p> -->
								</div>
								<div class="form-field form-required term-description-wrap">
									<label for="tag-description">Description</label>
									<textarea name="tag-description" id="tag-description" aria-required="true"><?php print (isset($form_data['description'])) ? $form_data['description'] : ''; ?></textarea>
									<!-- <p></p> -->
								</div>
								<div class="form-field form-required term-depth-wrap">
									<label for="tag-depth">Depth (Metres)</label>
									<input name="tag-depth" id="tag-depth" type="text" value="<?php print (isset($form_data['depth'])) ? $form_data['depth'] : ''; ?>" size="40" aria-required="true">
									<!-- <p></p> -->
								</div>
								<div class="form-field form-required term-buoy-type">
									<label for="tag-buoy-type">Buoy Type</label>
									<select name="tag-buoy-type" id="tag-buoy-type">
										<option value=''>Select a buoy type</option>
										<option value="datawell" <?php selected($form_data['buoy_type'], 'datawell'); ?>>Datawell</option>
										<option value="spoondrift" <?php selected($form_data['buoy_type'], 'spoondrift'); ?>>Spoondrift</option>
										<option value="triaxy" <?php selected($form_data['buoy_type'], 'triaxy'); ?>>Triaxy</option>
									</select>
									<!-- <p></p> -->
								</div>
								<div class="form-field form-required term-enabled-wrap">
									<label for="tag-enabled">Enabled</label>
									<input name="tag-enabled" id="tag-enabled" type="checkbox" value="1" <?php print (isset($form_data['visible'])) ? checked($form_data['visible']) : ''; ?> size="40" aria-required="true">
									<p>Unchecking this box will hide the buoy on the website.</p>
								</div>
								<div class="form-field form-required term-hide-location-wrap">
									<label for="tag-hide-location">Hide Location</label>
									<input name="tag-hide-location" id="tag-hide-location" type="checkbox" value="1" <?php print (isset($form_data['hide_location'])) ? checked($form_data['hide_location']) : ''; ?> size="40" aria-required="true">
									<p>Hide LAT/LNG on the sites frontend.</p>
								</div>
								<div class="form-required term-custom-lat-lng-wrap">
									<label for="tag-custom-lat">Custom LAT/LNG</label>
									<input name="tag-custom-lat" id="tag-custom-lat" type="text" value="<?php print (isset($form_data['custom_lat'])) ? $form_data['custom_lat'] : ''; ?>" class="small-text" size="5" aria-required="true"> , 
									<input name="tag-custom-lng" id="tag-custom-lng" type="text" value="<?php print (isset($form_data['custom_lng'])) ? $form_data['custom_lng'] : ''; ?>" class="small-text" size="5" aria-required="true">
									<p>Override buoy info LAT/LNG</p>
								</div>
								<div class="form-field form-required term-wave-height-increments">
									<label for="tag-wave-height-increments">Wave Height Increments (m) <small>Comma Separated</small></label>
									<input name="tag-wave-height-increments" id="tag-wave-height-increments" type="text" value="<?php print (isset($form_data['wave_height_increments'])) ? $form_data['wave_height_increments'] : ''; ?>" size="40" aria-required="true">
									<!-- <p></p> -->
								</div>
								<div class="form-field form-required term-intervals-wrap">
									<label for="tag-intervals">Homepage Graph Event Limit</label>
									<input name="tag-intervals" id="tag-intervals" type="text" value="<?php print (isset($form_data['homepage_graph_event_limit'])) ? $form_data['homepage_graph_event_limit'] : ''; ?>" size="40" aria-required="true">
									<p>Number of ticks on homepage graph</p>
								</div>
								<div class="form-field form-required term-image-wrap">
									<label for="tag-image">Image</label>
									<input name="tag-image" id="tag-image" type="text" value="<?php print (isset($form_data['image_url'])) ? $form_data['image_url'] : ''; ?>" size="40" aria-required="true">
								</div>
								<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php print (isset($form_data['id'])) ? 'Update Buoy' : 'Add New Buoy'; ?>"></p>
							</form>
						</div>
					</div>
				</div>
				<div id="col-right">
					<div id="col-wrap">
						<form method="post">
							<?php
								$this->buoys_obj->prepare_items();
								$this->buoys_obj->display(); 
							?>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'buoys',
			'default' => 5,
			'option'  => 'buoys_per_page'
		];

		add_screen_option( $option, $args );

		$this->buoys_obj = new Buoy_Info_List();
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
	Buoy_Info_Plugin::get_instance();
} );
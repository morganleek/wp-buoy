<?php

  $api_query_vars = array(
    'uwa_api_route',
    'buoy_id',
    'max_results',
    'start_date',
    'end_date'
  );

  /*
   * Routing Custom API URL to WP Ajax Request
  */

  function uwa_register_rewrites() {
    global $wp_rewrite;

    add_rewrite_rule( 'wp-buoy-api/(.*)?', 'index.php?uwa_api_route=$matches[1]', 'top' );
  }

  function uwa_api_init() {
    global $api_query_vars;
    uwa_register_rewrites();

    global $wp;
    foreach($api_query_vars as $var) {
      $wp->add_query_var( $var );  
    }
  }

  function uwa_api_loaded() {
    if ( empty( $GLOBALS['wp']->query_vars['uwa_api_route'] ) ) {
      return;
    }

    $errors = array();
    
    extract($GLOBALS['wp']->query_vars);
    switch($uwa_api_route) {
      case 'buoys':
        uwa_api_buoys();
        break;
      case 'wave-data-latest':
        // Validate Parameters
        uwa_api_wave_data_range(
          array(
            'buoy_id' => (isset($buoy_id)) ? $buoy_id : '',
            'max_results' => 1
          ), 
          $errors
        );
        break;
      case 'wave-data-range':
        // Validate Parameters
        $args = array();
        if(isset($buoy_id)) {
          $args['buoy_id'] = $buoy_id;
        }
        if(isset($max_results)) {
          $args['max_results'] = max_results;
        }
        if(isset($start_date)) {
          $args['start_date'] = $start_date;
        }
        if(isset($end_date)) {
          $args['end_date'] = $end_date;
        }

        uwa_api_wave_data_range($args, $errors);
        break;
    }

    if(!empty($errors)) {
      print json_encode(array('Errors' => $errors));
    }

    die();
  }

  add_action( 'init', 'uwa_api_init' );
  add_action( 'parse_request', 'uwa_api_loaded' );

  // add_action( 'parse_request', 'uwa_api_loaded' );

  /* 
   * Endpoint URL
   * /wp-buoy-api/buoys (GET)
   * 
   * Arguments
   * None.
   * 
   * Response
   * Returns a list of all visible buoys
  */

  function uwa_api_buoys() {
    global $wpdb;

    $buoys = $wpdb->get_results(
      'SELECT `id`, `buoy_id`, `title`, `description`, `depth`, `buoy_type`, `custom_lat`, `custom_lng`, `buoy_order`, `true_north_offset` 
      FROM `wp_buoy_info`
      WHERE `visible` = 1
      ORDER BY `buoy_order`'
    );

    print_r(json_encode($buoys));
  }

  /*
   * Endpoint URL 
   * /wp-buoy-api/wave-data-latest (POST)
   * 
   * Request parameters – application/json
   * required: buoy_id
   * 
   * Response
   * Returns most recent data sent by that buoy in JSON format
  */ 

  // function uwa_api_wave_data_latest($args = array(), &$errors = array()) {
  // }

  /*
   * Endpoint URL 
   * /wp-buoy-api/wave-data-range (POST)
   * 
   * Request parameters – application/json
   * required: buoy_id
   * optional: max_results  |  default: 20  |  max: 500
   * optional: start_date `Y-m-d H:i:s`  |  default: null  |  max: 500
   * optional: end_date `Y-m-d H:i:s`  |  default: now()  |  max: 500
   * 
   * Response
   * Returns most recent data sent by that buoy in JSON format over a time period
  */ 

  function uwa_api_wave_data_range($args = array(), &$errors = array()) {
    global $wpdb;

    $defaults = array(
      'buoy_id' => '', 
      'start_date' => date('Y-m-d H:i:s', 0), 
      'end_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
			'max_results' => 20
		);
    $_args = wp_parse_args($args, $defaults);

    if(empty($_args['buoy_id'])) {
      $errors[] = 'Required parameter buoy_id was not included.';
      return;
    }

    // Check if valid buoy and type;
    $buoy_info = $wpdb->get_row(
      $wpdb->prepare(
        'SELECT * FROM `wp_buoy_info`
        WHERE `buoy_id` = "%s"',
        $_args['buoy_id']
      )
    );

    // Check results
    if($wpdb->num_rows === 0) {
      $errors[] = "No buoy with the id of " . $_args['buoy_id'] . " exists.";
      return;
    }

    switch($buoy_info->buoy_type) {
      case 'spoondrift':
        $spoondrift_lookup_id = uwa_spoondrift_lookup_id($_args['buoy_id'], '');
        // Recent Waves
        $waves = $wpdb->get_results(
          $wpdb->prepare("	
            SELECT * FROM 
            (SELECT * FROM `{$wpdb->prefix}spoondrift_post_data_processed` WHERE spotter_id = '%s') AS P
            INNER JOIN
            (SELECT * FROM `{$wpdb->prefix}spoondrift_post_data_processed_waves` WHERE `timestamp` < '%s' AND `timestamp` > '%s') AS W
            ON P.id = W.post_data_processed_id
            ORDER BY W.`timestamp`
            max_results %d
            ", 
            $_args['buoy_id'],
            $_args['end_date'],
            $_args['start_date'],
            $_args['max_results']
          )
        );
        print json_encode($waves);
        break;
      case 'datawell':
        break;
      case 'triaxy':
        break;
    }
  }
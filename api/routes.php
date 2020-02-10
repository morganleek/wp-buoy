<?php
  /*
    * Routing Custom API URL to WP Ajax Request
  */

  $api_query_vars = array(
    'uwa_api_route',
    'buoy_id',
    'max_results',
    'start_date',
    'end_date'
  );

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
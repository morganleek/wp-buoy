<?php
	function uwa_sql_results_to_table($data, $title) {
		if($data) {
			$title = sanitize_title($title);
			
			$title_row = array();
			$content_rows = array();
			
			foreach($data[0] as $k => $v) {
				array_push($title_row, $k);
			}
			// Convert to CSV
			foreach($data as $row) {
				$array_row = array();
				foreach($row as $r) {
					array_push($array_row, $r);	
				}
				array_push($content_rows, $array_row);
			}
			
			$output = fopen("php://output",'w') or die("Can't open php://output");
			header("Content-Type:application/csv"); 
			header("Content-Disposition:attachment;filename=" . $title . ".csv"); 
			fputcsv($output, $title_row);
			foreach($content_rows as $content_row) {
			    fputcsv($output, $content_row);
			}
			fclose($output) or die("Can't close php://output");
		}
	}
	
	function uwa_terms_popup($message = '', $return = false) {
		$html = '';
		
		// Confirmation of ownership
		$html .= '<div id="dialog-confirm" title="Terms and Conditions">' . $message . '</div>';
		// CSV Generator Progress
		$html .= '<div id="dialog-csv-progress" title="CSV Generator Progress">
			<div id="progressbar"><div class="progress-label">Loading...</div></div>
			<div id="progressdescription"><small style="display: block;margin: 10px 0;font-size: 0.7em;font-style: italic;">When all parts have been processed a download will begin. This may take some time.</small></div>
		</div>';

		if($return) {
			return $html;
		}
		print $html;
	}

	function uwa_curl_post($url, array $post = NULL, array $options = array()) {
		// curl/7.29.0
		// Mozilla/5.0 (Windows; U;Windows NT 5.1; ru; rv:1.8.0.9) Gecko/20061206 Firefox/1.5.0.9

    $defaults = array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 4,
			CURLOPT_POSTFIELDS => http_build_query($post)
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch)) {
			trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

function uwa_query_array_to_key_value($query_array = array()) {
	$return = [];
	foreach($query_array as $q) {
		$q_temp = explode('=', $q);
		if(sizeof($q_temp) == 2) {
			$return[$q_temp[0]] = $q_temp[1];
		}
	}
	return $return;
}
 
function uwa_log($buoy, $message = '') {
	if($message === '') {
		// Get
		return get_option($buoy . '_log', '...');
	}
	else {
		// Set
		$_br = "&#13;&#10;";
		// Add newline and timestamp
		$message =  date('Y-m-d H:i:s') . ': ' . $message . $_br;
		// Limit log to 2000 lines
		$history = implode($_br, explode($_br, get_option($buoy . '_log', ''), 2000));
		// Update
		update_option( $buoy . '_log', $message . $history);
	}
}

function uwa_log_clear($buoy) {
	update_option( $buoy . '_log', '');
}
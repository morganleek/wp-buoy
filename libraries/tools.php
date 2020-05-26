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
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
	
	function uwa_terms_popup($message = '') {
		// Confirmation of ownership
		print '<div id="dialog-confirm" title="Terms and Conditions">' . $message . '</div>';
		// CSV Generator Progress
		print '<div id="dialog-csv-progress" title="CSV Generator Progress">
			<div id="progressbar"><div class="progress-label">Loading...</div></div>
			<div id="progressdescription"><small style="display: block;margin: 10px 0;font-size: 0.7em;font-style: italic;">When all parts have been processed a download will begin. This may take some time.</small></div>
		</div>';
	}
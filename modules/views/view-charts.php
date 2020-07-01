<?php 
	function generate_google_chart($bouy_id, $chart_id, $callback, $data_points, $max_wave, $max_peak, $direction_points, $modulus) {
		generate_google_chart_with_args(
			array(
				'bouy_id' => $bouy_id, 
				'chart_id' => $chart_id, 
				'callback' => $callback, 
				'data_points' => $data_points, 
				'max_wave' => $max_wave, 
				'max_peak' => $max_peak, 
				'direction_points' => $direction_points, 
				'modulus' => $modulus
			)
		);
	}
	function generate_google_chart_with_args($args = array()) {
		$html = '';

		$defaults = array(
			'bouy_id' => '', 
			'chart_id' => '', 
			'callback' => '', 
			'data_points' => '', 
			'max_wave' => '', 
			'max_peak' => '', 
			'direction_points' => '', 
			'modulus' => 0,
			'return' => false
		);

		$_args = wp_parse_args($args, $defaults);
		foreach($_args as $k => $_a) {
			if($_a === "") {
				return;
			}
		}
		extract($_args);

		$sanitized_buoy_id = str_replace('-', '_', sanitize_title($bouy_id));
		
	  $html .= '<div class="chart-surround">';
		  $html .= '<div id="' . $chart_id . '" class="ticks-chart"></div>';
	    foreach($direction_points as $k => $w) {
		    if($k % $modulus == 0) {
		    	$html .= '<div class="overlay-marker om-' . $sanitized_buoy_id . '-overlay-marker-' . $k . ' direction-' . $w . '">
		  	    <img src="' . get_template_directory_uri() . '/img/0.png" height="50">
		  	  </div>';
		  	}
	    }
	    $html .= '<div class="overlay-marker om-' . $sanitized_buoy_id . '-legend-marker direction-0">
  	    <img src="' . get_template_directory_uri() . '/img/0.png" width="25" height="25" style="width: 25px; height: 25px;">
  	  </div>';
	  
	  
			$waveTicks = array();
			$waveTickMax = ceil($max_wave / 4) * 4;
			$waveTickDivider = $waveTickMax / 4;
			for($i = 0; $i <= 5; $i++) {
				$waveTicks[] = $i * $waveTickDivider;
			}
			
			$peakTicks = array();
			$peakTickMax = ceil($max_peak / 4) * 4;
			$peakTickDivider = $peakTickMax / 4;
			for($i = 0; $i <= 5; $i++) {
				$peakTicks[] = $i * $peakTickDivider;
			}
			$data_points_json = json_encode($data_points);
			$html .= '<div class="ticks-data"
				data-buoy-id="' . $sanitized_buoy_id . '"
				data-wave-tick-max="' . $waveTickMax . '"
				data-wave-ticks="' . implode(',', $waveTicks) . '"
				data-peak-tick-max="' . $peakTickMax . '"
				data-peak-ticks="' . implode(',', $peakTicks) . '"
				data-data-points=\'' . $data_points_json . '\'
				></div>';
		$html .= '</div>';
		
		if($_args['return']) {
			return $html;
		}
		print $html;
	} 
	
	function generate_time_series_charts($buoy_id, $sig_wave_x, $sig_wave_y, $max_wave_x, $max_wave_y, $peak_period_x, $peak_period_y, $mean_period_x, $mean_period_y, $peak_direction_x, $peak_direction_y, $peak_directional_spread_x, $peak_directional_spread_y, $mean_direction_x, $mean_direction_y, $mean_directional_spread_x, $mean_directional_spread_y, $plotclick = false, $return = false) {
		$html = '';

		// Only show title if data exists
		// Height Graph
		$title_height = array();
		if(!empty($sig_wave_x)) { array_push($title_height, 'Significant Wave Height'); }
		if(!empty($max_wave_x)) { array_push($title_height, 'Max Wave Height'); }
		
		$html .= '<h5>' . implode(' &amp; ', $title_height) . '</h5>'; 
		$html .= '<div id="plot-sig-wave" class="plot"></div>';
		$html .= '<hr>';
		$html .= '<h5>Peak Period &amp; Mean Period</h5>';
		$html .= '<div id="plot-peak-period" class="plot"></div>';
		$html .= '<hr>';
		$html .= '<h5>Peak Direction</h5>'; // &amp; Peak Spread
		$html .= '<div id="plot-peak-direction" class="plot"></div>';

		// $html .= '<h5>Mean Direction &amp; Mean Spread</h5>';
		// $html .= '<div id="plot-mean-direction" class="plot"></div>';

		$html .= '<script type="text/javascript">';
			if($plotclick) {		 	
				$html .= 'function processPlotClick(data) {
					var date = "";
			    for(var i=0; i < data.points.length; i++){
			        date = data.points[i].x;
			    }
			    
			    var formatDate = new Date(date + ":00+0800");
			    
			    datawell_memplot_swap(\'' . $buoy_id . '\', formatDate);
				}';
			}
			$html .= 'var sigWaveX = [\'' . implode('\', \'', $sig_wave_x) . '\'];' . 
			'var sigWaveY = [\'' . implode('\', \'', $sig_wave_y) . '\'];' .
			'var maxWaveX = [\'' . implode('\', \'', $max_wave_x) . '\'];' . 
			'var maxWaveY = [\'' . implode('\', \'', $max_wave_y) . '\'];' .
			'var peakPeriodX = [\'' . implode('\', \'', $peak_period_x) . '\'];' . 
			'var peakPeriodY = [\'' . implode('\', \'', $peak_period_y) . '\'];' .
			'var meanPeriodX = [\'' . implode('\', \'', $mean_period_x) . '\'];' . 
			'var meanPeriodY = [\'' . implode('\', \'', $mean_period_y) . '\'];' .
			'var peakDirectionX = [\'' . implode('\', \'', $peak_direction_x) . '\'];' . 
			'var peakDirectionY = [\'' . implode('\', \'', $peak_direction_y) . '\'];' .
			'var peakDirectionalSpreadX = [\'' . implode('\', \'', $peak_directional_spread_x) . '\'];' . 
			'var peakDirectionalSpreadY = [\'' . implode('\', \'', $peak_directional_spread_y) . '\'];' .
			'var meanDirectionX = [\'' . implode('\', \'', $mean_direction_x) . '\'];' . 
			'var meanDirectionY = [\'' . implode('\', \'', $mean_direction_y) . '\'];' .
			'var meanDirectionalSpreadX = [\'' . implode('\', \'', $mean_directional_spread_x) . '\'];' . 
			'var meanDirectionalSpreadY = [\'' . implode('\', \'', $mean_directional_spread_y) . '\'];' .

			'var layout = {
				margin: {
			    l: 50,
			    r: 20,
			    b: 40,
			    t: 0,
			    pad: 2
			  },
			  height: 200,
			  legend: {
			    x: 0.6,
			    y: -0.75,
			    traceorder: \'normal\',
			    font: {
			      family: \'sans-serif\',
			      size: 12,
			      color: \'#000\'
			    },
			    bgcolor: \'#E2E2E2\',
			    bordercolor: \'#FFFFFF\',
			    borderwidth: 2
			  },
			  yaxis: {
			    title: \'Height (m)\',
			  }
			};' . 

			'var sigWaveTrace = {
				  x: sigWaveX,
				  y: sigWaveY,
				  mode: \'lines+markers\',
					name: \'Significant Wave Height (meters)\'
				};';
			
			if(!empty($max_wave_x)) {
				$html .= '
				var maxWaveTrace = {
				  x: maxWaveX,
				  y: maxWaveY,
				  mode: \'lines+markers\',
					name: \'Maximum Wave Height (meters)\'
				};
				
				var data = [sigWaveTrace, maxWaveTrace];';
			}
			else {
				$html .= 'var data = [sigWaveTrace];';
			}
			
			$html .= '
			var plotSigWave = document.getElementById(\'plot-sig-wave\');
			Plotly.newPlot(\'plot-sig-wave\', data, layout);
			
			plotSigWave.on(\'plotly_click\', function(data){
				processPlotClick(data);
			});
			
			layout.yaxis.title = \'Period (s)\';
			var peakPeriodTrace = {
				  x: peakPeriodX,
				  y: peakPeriodY,
				  mode: \'lines+markers\',
				  name: \'Peak Period (seconds)\'
				};
			var meanPeriodTrace = {
				  x: meanPeriodX,
				  y: meanPeriodY,
				  mode: \'lines+markers\',
				  name: \'Mean Period (seconds)\'
				};

			var data = [peakPeriodTrace, meanPeriodTrace];
			var plotPeakPeriod = document.getElementById(\'plot-peak-period\');
			Plotly.newPlot(\'plot-peak-period\', data, layout);
			
			plotPeakPeriod.on(\'plotly_click\', function(data){
				processPlotClick(data);
			});

			layout.yaxis.title = \'Direction (degrees)\';
			var peakDirectionTrace = {
				  x: peakDirectionX,
				  y: peakDirectionY,
				  mode: \'lines+markers\',
				  name: \'Peak Period (seconds)\'
				};
			var peakDirectionalSpreadTrace = {
				  x: peakDirectionalSpreadX,
				  y: peakDirectionalSpreadY,
				  mode: \'lines+markers\',
				  name: \'Peak Directional Spread (Degrees)\'
				};
				
			var data = [peakDirectionTrace]; // peakDirectionalSpreadTrace
			var plotPeakDirection = document.getElementById(\'plot-peak-direction\');
			Plotly.newPlot(\'plot-peak-direction\', data, layout);
			
			plotPeakDirection.on(\'plotly_click\', function(data){
				processPlotClick(data);
			});

			/*var meanDirectionTrace = {
				  x: meanDirectionX,
				  y: meanDirectionY,
				  mode: \'lines+markers\',
				  name: \'Mean Period (seconds)\'
				};
			var meanDirectionalSpreadTrace = {
				  x: meanDirectionalSpreadX,
				  y: meanDirectionalSpreadY,
				  mode: \'lines+markers\',
				  name: \'Mean Directional Spread (seconds)\'
				};

			var data = [meanDirectionTrace, meanDirectionalSpreadTrace];

			Plotly.newPlot(\'plot-mean-direction\', data, layout);*/
			
			window.onresize = function() {
		    Plotly.Plots.resize(\'plot-sig-wave\');
		    Plotly.Plots.resize(\'plot-peak-period\');
		    Plotly.Plots.resize(\'plot-peak-direction\');
			};

			' .
		'</script>';

		if($return) {
			return $html;
		}
		print $html;
	}
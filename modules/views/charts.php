<?php 
	function generate_google_chart($bouy_id, $chart_id, $callback, $data_points, $max_wave, $max_peak, $direction_points, $modulus) {
		$chart_id = sanitize_title($bouy_id) . '_chart_div';
	  print '<div class="chart-surround">';
		  print '<div id="' . $chart_id . '"></div>';
	    foreach($direction_points as $k => $w) {
		    if($k % $modulus == 0) {
		    	print '<div class="overlay-marker ' . sanitize_title($bouy_id) . '-overlay-marker-' . $k . ' direction-' . $w . '">
		  	    <img src="' . get_template_directory_uri() . '/img/0.png" height="50">
		  	  </div>';
		  	}
	    }
	    print '<div class="overlay-marker ' . sanitize_title($bouy_id) . '-legend-marker direction-0">
  	    <img src="' . get_template_directory_uri() . '/img/0.png" width="25" height="25" style="width: 25px; height: 25px;">
  	  </div>';
	  print '</div>';
	  
	  $waveTicks = array();
	  $waveTickMax = round($max_wave / 4) * 4;
	  $waveTickDivider = $waveTickMax / 4;
	  for($i = 0; $i <= 5; $i++) {
			$waveTicks[] = $i * $waveTickDivider;
	  }
	  
	  $peakTicks = array();
	  $peakTickMax = round($max_peak / 4) * 4;
	  $peakTickDivider = $peakTickMax / 4;
	  for($i = 0; $i <= 5; $i++) {
		  $peakTicks[] = $i * $peakTickDivider;
	  }
	  
		print '<script type="text/javascript">
			var global_' . $chart_id . ';
	
	  	google.charts.load(\'current\', {\'packages\':[\'line\', \'corechart\']});
      google.charts.setOnLoadCallback(' . $callback . ');
      
      $(window).on(\'resize\', function() {
	      ' . $callback . '();
      });

			function ' . $callback . '() {
			  var chartDiv = document.getElementById(\'' . $chart_id . '\');

			  var data = new google.visualization.DataTable();
			  data.addColumn(\'date\', \'Month\');
			  data.addColumn(\'number\', "Significant Wave Height");
			  data.addColumn({type: \'string\', role: \'tooltip\'});
			  data.addColumn(\'number\', "Peak Period and Direction");
			  data.addColumn({type: \'string\', role: \'tooltip\'});

			  data.addRows([' . $data_points . ']);

			  var chartOptions = {
			    title: \'\',
	        height: 230,
	        backgroundColor: { fill: "transparent" },
	        chartArea: {left: 45, top: 30, right: 45, bottom: 30},
	        // Gives each series an axis that matches the vAxes number below.
	        series: {
	          0: {targetAxisIndex: 0, color: \'#449d44\', type: \'area\'},
	          1: {targetAxisIndex: 1, color: \'transparent\', visibleInLegend: true}
	        },
	        vAxes: {
	          // Adds titles to each axis.
	          0: {
	          	title: \'Wave Height (m)\',
	          	viewWindow: {
		          	min: 0,
		          	max: ' . $waveTickMax . '
	          	},
	          	ticks: [' . implode(',', $waveTicks) . ']
	          },
	          1: {
	          	title: \'Peak Period (s)\',
	          	viewWindow: {
		          	min: 0,
		          	max: ' . $peakTickMax . '
	          	},
	          	ticks: [' . implode(', ', $peakTicks) . ']
	          }
	        },
	        hAxis: {
		        gridlines: {
						  count: -1,
						  units: {
						    days: {format: [\'MMM d\']},
						    hours: {format: [\'ha\', \'ha\']},
						  }
						},
						minorGridlines: {
						  units: {
						    hours: {format: [\'ha\', \'ha\']}
						  }
						}
	        }
			  };

			  function placeMarker(dataTable) {
				  var cli = this.getChartLayoutInterface();
			  	var chartArea = cli.getChartAreaBoundingBox();
			  	for(var i = 0; i < dataTable.getNumberOfRows(); i++) {
				  	if(i % ' . $modulus . ' == 0) {
				  	  document.querySelector(\'.' . sanitize_title($bouy_id) . '-overlay-marker-\' + i).style.top = Math.floor(cli.getYLocation(dataTable.getValue(i, 3), 1)) - 25 + "px";
				  	  document.querySelector(\'.' . sanitize_title($bouy_id) . '-overlay-marker-\' + i).style.left = Math.floor(cli.getXLocation(dataTable.getValue(i, 0))) - 25 + "px";
				  	}
			  	}
			  	
			  	// Place Legend Marker
			  	document.querySelector(\'.' . sanitize_title($bouy_id) . '-legend-marker\').style.top = Math.floor(cli.getBoundingBox("legendentry#1").top) - 8 + "px";
			  	document.querySelector(\'.' . sanitize_title($bouy_id) . '-legend-marker\').style.left = Math.floor(cli.getBoundingBox("legendentry#1").left) + 4 + "px";
	      };

			  function drawMaterialChart() {
			    var materialChart = new google.visualization.LineChart(chartDiv);
			    // global_' . $chart_id . ' = materialChart;
	        google.visualization.events.addListener(materialChart, \'ready\', placeMarker.bind(materialChart, data));
	        materialChart.draw(data, chartOptions);
			  }

			  drawMaterialChart();

			}

	  </script>';
	} 
	
	function generate_time_series_charts($buoy_id, $sig_wave_x, $sig_wave_y, $max_wave_x, $max_wave_y, $peak_period_x, $peak_period_y, $mean_period_x, $mean_period_y, $peak_direction_x, $peak_direction_y, $peak_directional_spread_x, $peak_directional_spread_y, $mean_direction_x, $mean_direction_y, $mean_directional_spread_x, $mean_directional_spread_y, $plotclick = false) {
		// Only show title if data exists
		// Height Graph
		$title_height = array();
		if(!empty($sig_wave_x)) { array_push($title_height, 'Significant Wave Height'); }
		if(!empty($max_wave_x)) { array_push($title_height, 'Max Wave Height'); }
		
		print '<h5>' . implode(' &amp; ', $title_height) . '</h5>'; 
		print '<div id="plot-sig-wave" class="plot"></div>';
		print '<hr>';
		print '<h5>Peak Period &amp; Mean Period</h5>';
		print '<div id="plot-peak-period" class="plot"></div>';
		print '<hr>';
		print '<h5>Peak Direction</h5>'; // &amp; Peak Spread
		print '<div id="plot-peak-direction" class="plot"></div>';

		// print '<h5>Mean Direction &amp; Mean Spread</h5>';
		// print '<div id="plot-mean-direction" class="plot"></div>';

		print '<script type="text/javascript">';
			if($plotclick) {		 	
				print 'function processPlotClick(data) {
					var date = "";
			    for(var i=0; i < data.points.length; i++){
			        date = data.points[i].x;
			    }
			    
			    var formatDate = new Date(date + ":00+0800");
			    
			    datawell_memplot_swap(\'' . $buoy_id . '\', formatDate);
				}';
			}
			print 'var sigWaveX = [\'' . implode('\', \'', $sig_wave_x) . '\'];' . 
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
				print '
				var maxWaveTrace = {
				  x: maxWaveX,
				  y: maxWaveY,
				  mode: \'lines+markers\',
					name: \'Maximum Wave Height (meters)\'
				};
				
				var data = [sigWaveTrace, maxWaveTrace];';
			}
			else {
				print 'var data = [sigWaveTrace];';
			}
			
			print '
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
	}
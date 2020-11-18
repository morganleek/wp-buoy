import $ from 'jquery';
// import Chart from '../../../node_modules/chartjs';
import Chart from 'chart.js';

$(function() {
	const charts = document.getElementsByClassName('chart-js-layout');
	if( charts.length > 0 ) {
		for( let i = 0; i < charts.length; i++ ) {
			const buoyID = charts[i].dataset['buoy'];

			// Ajax
			const init = {
				method: 'POST'
			};

			const params = {
				action: 'uwa_datawell_wave_points_json',
				buoy_id: buoyID,
				wave_from: '2020-10-04+22:00:00',
				wave_until: '2020-10-01+22:00:00',
				time_adjustment: '+8' // Replace
			};

			let paramsString = Object.keys(params).map(function(key) {
				return key + '=' + params[key];
			}).join('&');

			fetch(ajax_object.ajax_url + '?' + paramsString, init)
			.then((response) => {
			  return response.json();
			})
			.then((waves) => {
				// Generate Chart
				if( waves.length > 0 ) {
					let buoyID = waves[0].buoy_id
					uwaGenerateChart( buoyID, waves );
				}
			})
			.catch((e) => {
				// Error
				console.log(e);
			});
		}
	}

	function uwaGenerateBuoyDate( timestamp, utcOffset ) {
		const timeMilliseconds = timestamp * 1000; // Milliseconds for JS
		const utcDate = moment(timeMilliseconds);
		return utcDate.utcOffset( utcOffset ).toDate();
	}

	function uwaGenerateBuoyDateString( timestamp, utcOffset, format = 'MM/DD/YYYY HH:mm' ) {
		const timeMilliseconds = timestamp * 1000; // Milliseconds for JS
		const utcDate = moment(timeMilliseconds);
		return utcDate.utcOffset( utcOffset ).format( format );
	}

	function uwaGenerateChart( buoyID, waves ) {
		let buoyOffset = '+8'; // Replace
		let waveHeightData = [];
		let peakPeriodData = [];
		let chartLabels = [];
		if( waves.length ) {
			for( let i = 0; i < waves.length; i++ ) {
				let time = uwaGenerateBuoyDate( waves[i].time, buoyOffset );
				
				switch( i % 10 ) {
					case 0:
						chartLabels.push( uwaGenerateBuoyDateString( waves[i].time, buoyOffset, 'MM/DD' ) );
						break;
					case 2:
					case 4:
					case 6:
					case 8:
						chartLabels.push( uwaGenerateBuoyDateString( waves[i].time, buoyOffset, 'H:mm a' ) );
						break;
					default:
						chartLabels.push( '' );
						break;
				}
				
				waveHeightData.push( {
					x: time,
					y: waves[i].significant_wave_height,
				} );
				peakPeriodData.push( {
					x: time,
					y: waves[i].peak_period,
				} );
			}

			// Draw Chart
			// var color = Chart.helpers.color;
			var config = {
				type: 'line',
				data: {
					labels: chartLabels,
					datasets: [
						{
							label: 'Wave Height',
							backgroundColor: 'rgba(60, 118, 61, 0.7)',
							borderColor: 'rgba(60, 118, 61, 1)',
							fill: true,
							data: waveHeightData,
							yAxisID: 'y-axis-1',
						}, {					
							label: 'Peak Period',
							backgroundColor: 'rgba(238, 238, 238, 0.7)',
							borderColor: 'rgba(238, 238, 238, 1)',
							fill: false,
							data: peakPeriodData,
							yAxisID: 'y-axis-2',
						} 
					]
				},
				options: {
					responsive: true,
					hoverMode: 'index',
					stacked: false,
					title: {
						display: true,
						text: 'Significant Wave Height'
					},
					scales: {
						yAxes: [{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'left',
							id: 'y-axis-1',
						}, {
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'right',
							id: 'y-axis-2',

							// grid line settings
							gridLines: {
								drawOnChartArea: false, // only want the grid lines for one axis to show up
							},
						}],
					}
					// title: {
					// 	text: 'Significant Wave Height'
					// },
					// scales: {
					// 	xAxes: [{
					// 		type: 'time',
					// 		time: {
					// 			parser: 'MM/DD/YYYY HH:mm',
					// 			// round: 'day'
					// 			tooltipFormat: 'll HH:mm'
					// 		},
					// 		scaleLabel: {
					// 			display: true,
					// 			labelString: 'Date'
					// 		}
					// 	}],
					// 	yAxes: [{
					// 		scaleLabel: {
					// 			display: true,
					// 			labelString: 'Wave Height (m)'
					// 		}
					// 	}]
					// },
				}
			};

			// Load chart
			window.onload = function() {
				var ctx = document.getElementById( 'canvas-' + buoyID ).getContext( '2d' );
				window.myLine = new Chart(ctx, config);
				
			};
		}
		else {
			// No waves values
			return;
		}		
	}

	// const time = 1601562600; // UTC Unix Timestamp
	// const timeMilliseconds = time * 1000; // Milliseconds for JS
	// const localDate = moment(timeMilliseconds); // new Date(timeMilliseconds);
	// const timezoneOffset = '+10';
	
	// // Formatted as local
	// console.log(localDate.format());

	// // Convert to buoy local time
	// console.log(localDate.utcOffset(timezoneOffset).format());
});


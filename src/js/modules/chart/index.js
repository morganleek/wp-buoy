import $ from 'jquery';
import { uwaFetchChartData } from './data';
import { uwaInitializeRange } from './range';

$(function() {
	const charts = document.getElementsByClassName('chart-js-layout');
	if( charts.length > 0 ) {
		// Fetch chart data on load
		for( let i = 0; i < charts.length; i++ ) {
			const buoyID = charts[i].dataset['buoy'];
			const params = {
				action: 'uwa_datawell_wave_points_json',
				buoy_id: buoyID,
				wave_from: '2020-10-04+08:00:00',
				wave_until: '2020-10-03+00:00:00',
				time_adjustment: '+8' // Replace
			};

			uwaFetchChartData( params );

			// Date range
			if( charts[i].getElementsByClassName('calendar-trigger').length ) {
				console.log(charts[i].getElementsByClassName('calendar-trigger')[0]);
				uwaInitializeRange( charts[i].getElementsByClassName('calendar-trigger')[0] );
			}
		}
	}
});
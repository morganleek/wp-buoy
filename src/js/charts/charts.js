import $ from 'jquery';
import Chart from '../../../node_modules/chartjs';

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
				time_adjustment: '+8'
			};

			let paramsString = Object.keys(params).map(function(key) {
				return key + '=' + params[key];
			}).join('&');

			fetch(ajax_object.ajax_url + '?' + paramsString, init)
			.then((response) => {
			  return response.json();
			})
			.then((waves) => {
				// // text is the response body
				// console.log('Text');
				// console.log(text);
				
			})
			.catch((e) => {
				// error in e.message
				console.log(e);
			});
		}
	}
});


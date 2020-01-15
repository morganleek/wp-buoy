var map;
function initMap() {
	if(document.getElementById('map') !== null) {
		map = new google.maps.Map(document.getElementById('map'), {
		  center: {lat: parseFloat(ajax_object.starting_lat), lng: parseFloat(ajax_object.starting_lng)},
		  zoom: ($(window).width() < 1200) ? 4 : 5
		});

		/*
		** Markers 
		*/
		// Global Pointers
		for(var i = 0; i < global_points.length; i++) {
			var point = new MarkerWithLabel({
				position: {lat: parseFloat(global_points[i][2]), lng: parseFloat(global_points[i][3])},
				// draggable: true,
				// raiseOnDrag: true,
				map: map,
				title: global_points[i][6],
				labelContent: global_points[i][5],
				labelAnchor: new google.maps.Point(0, -2),
				labelClass: "maps-label", // the CSS class for the label
				labelStyle: {opacity: 0.9}
			});
			
			point.addListener('click', function() {
				window.location = this.getTitle();
			});
		}
	}
}

$.when( $.ready ).then(function() {
	initMap();
});
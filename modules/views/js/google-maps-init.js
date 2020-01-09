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
		
		// Spoondrift
		for(var i = 0; i < spoondrift_points.length; i++) {
			var point = new MarkerWithLabel({
       position: {lat: parseFloat(spoondrift_points[i][2]), lng: parseFloat(spoondrift_points[i][3])},
       // draggable: true,
       // raiseOnDrag: true,
       map: map,
       title: spoondrift_points[i][6],
       labelContent: spoondrift_points[i][5],
       labelAnchor: new google.maps.Point(0, -2),
       labelClass: "maps-label", // the CSS class for the label
       labelStyle: {opacity: 0.9}
     });
			
			point.addListener('click', function() {
				window.location = this.getTitle();
			});
		}
		
		// Datawell
		for(var j = 0; j < datawell_points.length; j++) {
			var point = new MarkerWithLabel({
       position: {lat: parseFloat(datawell_points[j][2]), lng: parseFloat(datawell_points[j][3])},
       map: map,
       title: datawell_points[j][6],
       labelContent: datawell_points[j][5],
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
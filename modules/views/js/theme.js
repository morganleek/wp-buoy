var csv = [];
var globalCsvs = [], globalCsvsTotal = 0, globalCsvCache = "";
var dialog, progressbar, progressLabel;
var csvKeepGoing = false;
var csvHeader = "", csvLastDate = "";

// function uwa_update_memplot_buttons(response) {
// 	response = response.replace('  ', '');
// 	var r = parseInt(response);
// 	var j = r + 1;
// 	if(r >= 0) {
// 		$('.csv-form-' + j + 'd [name="send-memplot"]').val(csv[r]);		
// 		$('.csv-form-' + j + 'd button').removeClass('disabled').removeAttr('disabled');
// 	}
// 	else {
// 		$('.csv-form-' + j + 'd button').addClass('disabled').attr('disabled', '');
// 	}
// }

// 1D + 2D CSVs
// function uwa_update_memplot_csv(jpg) {
// 	// Generate CSV Names
// 	csv.push(jpg.replace('.jpg', '.csv').replace('plots', 'data1D').replace('plot', 'data1D'));
// 	csv.push(jpg.replace('.jpg', '.csv').replace('plots', 'data').replace('plot', 'data'));
// 	
// 	// Check if they exist
// 	for(var i = 0; i < csv.length; i++) {
// 		var id = Object.keys(csv)[i];
// 		var key = csv[i];
// 		jQuery.post('/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=csv_exists&key=' + key + '&id=' + id, '', uwa_update_memplot_buttons);
// 	}
// }

Number.prototype.pad = function(size) {
  var s = String(this);
  while (s.length < (size || 2)) {s = "0" + s;}
  return s;
}

// Memplot Swap
function datawell_memplot_swap(buoy_id, date) {
	// Adjust as DB Stores in GMT
	date.setHours(date.getHours() - 8);
	
	var jsDate = date.getFullYear() + '-' + (date.getMonth() + 1).pad(2) + '-' + date.getDate().pad(2) + ' ' + date.getHours().pad(2) + ':' + date.getMinutes().pad(2);
	
	var data = {
		'action': 'fetch_spectrum',
		'buoy_id': buoy_id,
		'date': jsDate
	};
	
	$('.memplot').addClass('loading');
	$('.panel-memplot .date').text(jsDate);
	
	jQuery.post(ajax_object.ajax_url, data, function(response) {
		response = response.replace('  ', '');
		if(response !== '0') {
			var getImage = new Image();
			getImage.onload = function() {
				$('.memplot img')[0].src = this.src;
				$('.memplot').removeClass('loading');
			};
			getImage.src = '/wp-admin/admin-ajax.php?action=uwa_datawell_aws&do=image_fetch&key=' + response;
			// Update hidden memplot value
			$('[name="memplot"]').val(response);
			// Update 1D and 2D csv values
			// uwa_update_memplot_csv(response);
		}
		else {
			alert('No Frequency-Directional Spectrum for this time');
			$('.memplot').addClass('loading');
		}
	});
}

// Terms Agreement for all downloads
function uwa_trigger_tac($form, type, spectrum) {
	// Terms Agree
	dialog = $( "#dialog-confirm" ).dialog({
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
      "Accept": function() {
	      if(type == "csv") {
		      $( this ).dialog( "close" );
		      $form.find('input[name="csv-dates"]').val($('input[name="dates"]').val());
					$form.submit();
				}
				else {
					$( this ).dialog( "close" );
					uwa_trigger_spec_progress($form, spectrum);
				}
      },
      "Cancel": function() {
        $( this ).dialog( "close" );
        uwa_reset_csv_downloads();
      }
    },
    close: function( event, ui ) {
	    uwa_reset_csv_downloads();
    }
  });
}

// Progress Dialog for Multipart Downloads
function uwa_trigger_spec_progress($form, spectrum) {
	// Dialog Box
	$( "#dialog-csv-progress" ).dialog({
		resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    buttons: {
	    "Cancel": function() {
        $( this ).dialog( "close" );
        uwa_reset_csv_downloads();
      }
    },
    close: function( event, ui ) {
	    uwa_reset_csv_downloads();
    }
	});

	progressbar = $( "#progressbar" ),
	progressLabel = $( ".progress-label" );

	progressbar.progressbar({
		value: 0,
		change: function() {
			progressLabel.text( progressbar.progressbar( "value" ) + "%" );
		},
		complete: function() {
			progressLabel.text( "Complete!" );
		}
	});
	
	uwa_datawell_spectrum_fetch($('[name="dates"]').val(), $('[name="csv-buoy_id"]').val(), spectrum);
	// console.log('uwa_datawell_spectrum_fetch');
	// console.log($('[name="dates"]').val());
	// console.log($('[name="csv-buoy_id"]').val());
	// console.log(spectrum);
}

function uwa_file_download(filename, data) {
  var blob = new Blob([data], {type: 'text/csv'});
  if(window.navigator.msSaveOrOpenBlob) {
    window.navigator.msSaveBlob(blob, filename);
  }
  else{
    var elem = window.document.createElement('a');
    elem.href = window.URL.createObjectURL(blob);
    elem.download = filename;        
    document.body.appendChild(elem);
    elem.click();        
    document.body.removeChild(elem);
  }
}

function uwa_iterate_csvs_and_concat() {
	if(globalCsvs === 0) {
		window.alert('No specturm data currently available for this time period');
		progressLabel.text( "No Data" );
		uwa_reset_csv_downloads(); // Stop download and clear settings
	}
	
	if(csvKeepGoing) {
		if(globalCsvs.length > 0) {
			var key = globalCsvs.pop();
			
			var current = globalCsvsTotal - globalCsvs.length;
			// console.log('Processing ' + current + ' of ' + globalCsvsTotal);
			progressbar.progressbar("value", Math.floor( current / globalCsvsTotal * 100 ));
			// progressbar.progressbar( "option", {
	    //   value: Math.floor( current / globalCsvsTotal * 100 )
	    // });
	
			var data = {
				action: 'uwa_datawell_aws',
				do: 'csv_download',
				key: key
			};
			
			console.log(key);
			
			jQuery.get(ajax_object.ajax_url, data, function(response) {
				// Crop first 10 lines
				var crop = response.split("\n");
				
				// Start recording after 10 lines
				var headerTemp = crop.splice(0, 10).join("\n");
				csvLastDate = headerTemp.split("\n").splice(2, 1).join();
				if(csvHeader.length === 0) {
					csvHeader = headerTemp;
				}
				
				// Append a date stamp before each data dump
				globalCsvCache += headerTemp.split("\n").splice(2, 1).join() + "\n" + crop.join("\n");
				
				uwa_iterate_csvs_and_concat();
			});
	
		}
		else {
			// Contact all CSVs
			progressLabel.text( "Complete!" );
			
			// Add end date
			csvLastDate = csvLastDate.replace("Data_time:", "");
			csvHeader = csvHeader.replace("UTC", "UTC - " + csvLastDate);
			
			uwa_file_download('spectrum.csv', csvHeader + "\n" + globalCsvCache);
			uwa_reset_csv_downloads(); // Stop download and clear settings
		}
	}
	else {
		uwa_reset_csv_downloads();
	}
}

function uwa_reset_csv_downloads() {
	csvKeepGoing = false;
	csvHeader = "";
	csvLastDate = "";
	globalCsvCache = '';
	globalCsvsTotal = 0;
}

function uwa_datawell_spectrum_fetch(dates, bouyID, spectrum) {
	// Fetch all in a date range
	var data = {
		action: 'uwa_datawell_aws',
		do: 'spectrum_csvs',
		dates: encodeURIComponent(dates), // .replace (" - ", "%20-%20"),
		buoy_id: bouyID, 
		spectrum: spectrum
	};
	
	jQuery.getJSON(ajax_object.ajax_url, data, function(response) {
		// response = response.replace('  ', '');
		if(response !== '0') {
			globalCsvs = response;
			globalCsvsTotal = response.length;
			console.log('Found ' + response.length + ' items');
			
			// Loop through and download each
			csvKeepGoing = true;
			
			// Check they exist
			
			// Grab and Concat
			uwa_iterate_csvs_and_concat();
			
			// Update 1D and 2D csv values
			// uwa_update_memplot_csv(response);
		}
		else {
			alert('No Frequency-Directional Spectrum for this time');
			$('.memplot').addClass('loading');
		}
	});

}


$.when( $.ready ).then(function() {
  // Dom Ready..
	$('.csv-form button').not('[disabled]').on('click', function(e) {
		e.preventDefault();
		
		var type = '', spectrum = '';
		
		if($(this).parent().hasClass('csv-form-2d') || $(this).parent().hasClass('csv-form-1d')) {
			type = 'specrum';
			spectrum = ($(this).parent().hasClass('csv-form-2d')) ? '2d' : '1d';
		}
		else {
			type = 'csv';
		}
		
		var $form = $(this).parent();
		uwa_trigger_tac($form, type, spectrum);
	});	
	
	$('.csv-form button[disabled]').on('click', function(e) { e.preventDefault(); });
	
	$('.ui-dialog-titlebar-close').on('click', function() {
		console.log('a');
		uwa_reset_csv_downloads();
	});
	
	// Setup 1D + 2D CSVs
	// if(!!$('input[name="memplot"]').length) {
	// 	var memplot = $('input[name="memplot"]').val();
		
	// 	uwa_update_memplot_csv(memplot);
	// }
});

$(window).on("load", function () {
  // Window Ready...
});

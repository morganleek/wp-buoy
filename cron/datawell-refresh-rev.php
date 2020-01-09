<?php
	// Server URL
	$server = 'http://wawaves.org';
	// Trigger AJAX Fetch
	$xmlData = file_get_contents($server . '/wp-admin/admin-ajax.php?action=update_datawell_rev');
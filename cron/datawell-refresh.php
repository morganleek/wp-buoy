<?php
	// Server URL
	$server = 'http://' . $_SERVER['HTTP_HOST'];
	// Trigger AJAX Fetch
	$xmlData = file_get_contents($server . '/wp-admin/admin-ajax.php?action=update_datawell');
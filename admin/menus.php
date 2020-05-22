<?php
	// Top Level Menu
	function uwa_options_page_html() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
	    <h1><?= esc_html(get_admin_page_title()); ?></h1>
			<form method="post" action="options.php"> 
				<?php 
					settings_fields( 'uwa-buoy-options' ); 
					do_settings_sections( 'uwa-buoy-options' );
				?>
				<h2>Global</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="uwa_map_starting_position_lat">Map Starting Position<br><small>(LAT, LNG)</small></label></th>
							<td><input name="uwa_map_starting_position_lat" type="text" id="uwa_map_starting_position_lat" value="<?php echo esc_attr( get_option('uwa_map_starting_position_lat') ); ?>" class="small-text"> <input name="uwa_map_starting_position_lng" type="text" id="uwa_map_starting_position_lng" value="<?php echo esc_attr( get_option('uwa_map_starting_position_lng') ); ?>" class="small-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_copyright_message">Download Copyright Message</label></th>
							<td>
								<textarea name="uwa_copyright_message" rows="10" cols="100" id="uwa_copyright_message" class="text-large code"><?php echo esc_attr( get_option('uwa_copyright_message') ); ?></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_labels">Label Overrides</label></th>
							<td>
								<textarea name="uwa_label_overrides" rows="5" cols="100" id="uwa_label_overrides" class="text-large code"><?php echo esc_attr( get_option('uwa_label_overrides') ); ?></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_google_maps_api">Google Maps API Key</label></th>
							<td><input name="uwa_google_maps_api" type="text" id="uwa_google_maps_api" value="<?php echo esc_attr( get_option('uwa_google_maps_api') ); ?>" class="regular-text"></td>
						</tr>
					</tbody>
				</table>
				<h2>Datawell</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="uwa_datawell_s3_key">AWS S3 Key</label></th>
							<td><input name="uwa_datawell_s3_key" type="text" id="uwa_datawell_s3_key" value="<?php echo esc_attr( get_option('uwa_datawell_s3_key') ); ?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_datawell_s3_secret">AWS S3 Secret</label></th>
							<td><input name="uwa_datawell_s3_secret" type="text" id="uwa_datawell_s3_secret" value="<?php echo esc_attr( get_option('uwa_datawell_s3_secret') ); ?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_datawell_s3_region">AWS S3 Region</label></th>
							<td><input name="uwa_datawell_s3_region" type="text" id="uwa_datawell_s3_region" value="<?php echo esc_attr( get_option('uwa_datawell_s3_region') ); ?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_datawell_s3_bucket">AWS S3 Bucket</label></th>
							<td><input name="uwa_datawell_s3_bucket" type="text" id="uwa_datawell_s3_bucket" value="<?php echo esc_attr( get_option('uwa_datawell_s3_bucket') ); ?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_datawell_time_adjustment">Datawell Time Adjustment (GMT)</label></th>
							<td><input name="uwa_datawell_time_adjustment" type="text" id="uwa_datawell_time_adjustment" value="<?php echo esc_attr( get_option('uwa_datawell_time_adjustment') ); ?>" class="regular-text"></td>
						</tr>
					</tbody>
				</table>
				<h2>Triaxy</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="uwa_triaxy_time_adjustment">Triaxy Time Adjustment (GMT)</label></th>
							<td><input name="uwa_triaxy_time_adjustment" type="text" id="uwa_triaxy_time_adjustment" value="<?php echo esc_attr( get_option('uwa_triaxy_time_adjustment') ); ?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_triaxy_ftps">Triaxy FTPs</label></th>
							<td>
								<textarea name="uwa_triaxy_ftps" rows="10" cols="100" id="uwa_triaxy_ftps" class="text-large code"><?php echo esc_attr( get_option('uwa_triaxy_ftps') ); ?></textarea>
								<p class="description">
									Format:<br>
									<code>{ "ftp": [{ "serial": "A0-123456789", "url": "example.com", "port": "21", "user": "example", "pass": "strongpassword" }, {...}]}</code>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<h2>Spotter (Spoondrift)</h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="uwa_spoondrift_time_adjustment">Spoondrift Time Adjustment (GMT)</label></th>
							<td><input name="uwa_spoondrift_time_adjustment" type="text" id="uwa_spoondrift_time_adjustment" value="<?php echo esc_attr( get_option('uwa_spoondrift_time_adjustment') ); ?>" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="uwa_spoondrift_post_logging">Spoondrift POST Receieved File Logging</label></th>
							<td>
								<label for="uwa_spoondrift_post_logging"><input name="uwa_spoondrift_post_logging" type="checkbox" id="uwa_spoondrift_post_logging" value="1" <?php checked( get_option( 'uwa_spoondrift_post_logging' ) ); ?> class="regular-text"> Enabled</label>
								<p class="description">
									This will dump all received POST data into a file in <code><?php print UWA__PLUGIN_DIR; ?>modules/ajax/</code>
								</p>	
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
    </div>
	  <?php
	}
	
	// Register Settings
	function uwa_register_settings() {
		register_setting( 'uwa-buoy-options', 'uwa_copyright_message' );
		register_setting( 'uwa-buoy-options', 'uwa_google_maps_api' );
		register_setting( 'uwa-buoy-options', 'uwa_label_overrides' );
		register_setting( 'uwa-buoy-options', 'uwa_datawell_s3_key' );
		register_setting( 'uwa-buoy-options', 'uwa_datawell_s3_secret' );
		register_setting( 'uwa-buoy-options', 'uwa_datawell_s3_region' );
		register_setting( 'uwa-buoy-options', 'uwa_datawell_s3_bucket' );
		register_setting( 'uwa-buoy-options', 'uwa_map_starting_position_lat' );
		register_setting( 'uwa-buoy-options', 'uwa_map_starting_position_lng' );
		register_setting( 'uwa-buoy-options', 'uwa_spoondrift_time_adjustment' );
		register_setting( 'uwa-buoy-options', 'uwa_spoondrift_post_logging' );
		register_setting( 'uwa-buoy-options', 'uwa_datawell_time_adjustment' );
		register_setting( 'uwa-buoy-options', 'uwa_triaxy_time_adjustment' );
		register_setting( 'uwa-buoy-options', 'uwa_triaxy_ftps');
	}
	
	// Include Menu
	function uwa_options_page() {
    add_menu_page(
      'Buoy Dashboard',
      'Buoy',
      'manage_options',
      'uwa',
      'uwa_options_page_html',
      'dashicons-admin-site',
      20
    );
    
    add_submenu_page(
      'uwa',
      'Buoy Settings',
      'Settings',
      'manage_options',
      'uwa',
      'uwa_options_page_html'
    );
	}
	add_action('admin_menu', 'uwa_options_page');
	add_action('admin_init', 'uwa_register_settings');
	
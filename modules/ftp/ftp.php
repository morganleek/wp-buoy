<?php
	use League\Flysystem;

	function ftp_test() {
		$adapter = new Flysystem\Ftp\FtpAdapter(
			// Connection options
			Flysystem\Ftp\FtpConnectionOptions::fromArray([
        'host' => 'ftp.axys-aps.com', // required
        'root' => "/", // required
        'username' => 'FTPTAB04022', // required
        'password' => 'BUFpfqP89UxL', // required
        'port' => 21,
        'ssl' => true,
        'timeout' => 90,
        'utf8' => false,
        'passive' => true,
        'transferMode' => FTP_BINARY,
        'systemType' => 'windows', // 'windows' or 'unix'
        'ignorePassiveAddress' => null, // true or false
        'timestampsOnUnixListingsEnabled' => false, // true or false
        'recurseManually' => true // true 
			])
		);

		// The FilesystemOperator
		$filesystem = new Flysystem\Filesystem($adapter);
		
		$paths = $filesystem->listContents('/G3-3997764076898085253')
			->sortByPath()
			->toArray();

		if( $paths ) {
			foreach( $paths as $path ) {
				var_dump( $path->isDir() );
				var_dump( $path->path() );

			}
		}


		// try {
		// 	$response = $filesystem->read( "/" );
		// } catch (Flsysytem\FilesystemException | Flsysytem\UnableToReadFile $exception) {
		// 	print "Error occurred";
		// }

		print 'FTP Test';
		wp_die();
	}

	add_action( 'wp_ajax_ftp_test', 'ftp_test' );
	add_action( 'wp_ajax_nopriv_ftp_test', 'ftp_test' );
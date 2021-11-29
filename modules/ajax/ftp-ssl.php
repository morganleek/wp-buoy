<?php
	// Flysystem via composer
	require_once __DIR__ . '/../../vendor/autoload.php';
  
	use League\Flysystem\FTP;
	use League\Flysystem\UnixVisibility;

	$adapter = new FtpAdapter(
    // Connection options
    FTP\FtpConnectionOptions::fromArray([
        'host' => 'hostname', // required
        'root' => '/root/path/', // required
        'username' => 'username', // required
        'password' => 'password', // required
    ]),
    new FTP\FtpConnectionProvider(),
    new FTP\NoopCommandConnectivityChecker(),
    new UnixVisibility\PortableVisibilityConverter()
);

// print 'hello';

// // The FilesystemOperator
// $filesystem = new League\Flysystem\Filesystem($adapter);

	// use League\Flysystem;
	// use League\Flysystem\Ftp\FtpConnectionOptions;
	// use League\Flysystem\FTP\FtpConnectionProvider;
	// use League\Flysystem\FTP\NoopCommandConnectivityChecker;
	// use League\Flysystem\UnixVisibility\PortableVisibilityConverter;


	// // The internal adapter
	// $adapter = new FtpAdapter(
	// 	// Connection options
	// 	FtpConnectionOptions::fromArray([
	// 			'host' => 'ftp.axys-aps.com', // required
	// 			'root' => '/', // required
	// 			'username' => 'FTPTAB04022', // required
	// 			'password' => 'BUFpfqP89UxL', // required
	// 	]),
	// 	new FtpConnectionProvider(),
	// 	new NoopCommandConnectivityChecker(),
	// 	new PortableVisibilityConverter()
	// );

	// // The FilesystemOperator
	// $filesystem = new League\Flysystem\Filesystem($adapter);

	// // set up basic ssl connection
	// $ftp = ftp_ssl_connect( "ftp.axys-aps.com", 21 );

	// // login with username and password
	// $login_result = ftp_login($ftp, "FTPTAB04022", "BUFpfqP89UxL");

	// if (!$login_result) {
	// 		// PHP will already have raised an E_WARNING level message in this case
	// 		die("can't login");
	// }

	// // $buff = ftp_rawlist($ftp, 'G3-3997764076898085253/');
	// $buff = ftp_exec($ftp, "LIST -a");

	// // close the ssl connection
	// ftp_close($ftp);

	// var_dump( $buff );
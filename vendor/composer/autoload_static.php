<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita3682c14e186fa4066f859f32f88e60b
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'League\\MimeTypeDetection\\' => 25,
            'League\\Flysystem\\Ftp\\' => 21,
            'League\\Flysystem\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'League\\MimeTypeDetection\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/mime-type-detection/src',
        ),
        'League\\Flysystem\\Ftp\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem-ftp',
        ),
        'League\\Flysystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita3682c14e186fa4066f859f32f88e60b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita3682c14e186fa4066f859f32f88e60b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita3682c14e186fa4066f859f32f88e60b::$classMap;

        }, null, ClassLoader::class);
    }
}

<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita14cc883d48cd42bf649acebf7e6c603
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Services\\' => 9,
        ),
        'P' => 
        array (
            'Predis\\' => 7,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'C' => 
        array (
            'Controllers\\' => 12,
            'Classes\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Services\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Services',
        ),
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Controllers',
        ),
        'Classes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita14cc883d48cd42bf649acebf7e6c603::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita14cc883d48cd42bf649acebf7e6c603::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita14cc883d48cd42bf649acebf7e6c603::$classMap;

        }, null, ClassLoader::class);
    }
}

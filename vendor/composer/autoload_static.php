<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf4d4c9a4472cd265624b36a0095744f2
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Db\\' => 3,
        ),
        'C' => 
        array (
            'Classes\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Db\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Db',
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitf4d4c9a4472cd265624b36a0095744f2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf4d4c9a4472cd265624b36a0095744f2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf4d4c9a4472cd265624b36a0095744f2::$classMap;

        }, null, ClassLoader::class);
    }
}

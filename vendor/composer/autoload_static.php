<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit428237b207f1a6b3a47c0c67fc11c0ad
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Creditable\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Creditable\\' => 
        array (
            0 => __DIR__ . '/..' . '/evalue8bv/creditable-paywall/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit428237b207f1a6b3a47c0c67fc11c0ad::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit428237b207f1a6b3a47c0c67fc11c0ad::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit428237b207f1a6b3a47c0c67fc11c0ad::$classMap;

        }, null, ClassLoader::class);
    }
}

<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit390b6913575e81eb5b5b5e7d721bbc30
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\PasswordHasher\\' => 33,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\PasswordHasher\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/password-hasher',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit390b6913575e81eb5b5b5e7d721bbc30::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit390b6913575e81eb5b5b5e7d721bbc30::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit390b6913575e81eb5b5b5e7d721bbc30::$classMap;

        }, null, ClassLoader::class);
    }
}

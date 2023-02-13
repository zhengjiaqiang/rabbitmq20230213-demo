<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitd3e24c997c091f5e6785a8e7c48e8c0c
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitd3e24c997c091f5e6785a8e7c48e8c0c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitd3e24c997c091f5e6785a8e7c48e8c0c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitd3e24c997c091f5e6785a8e7c48e8c0c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

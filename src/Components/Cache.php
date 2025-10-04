<?php

namespace Cleup\Configuration\Components;

use Cleup\Cache\Drivers\LocalDriver;

class Cache
{
    private static ?\Cleup\Cache\Cache $cacheInstance = null;

    /**
     * Get cache instance
     * 
     * @return \Cleup\Cache\Cache
     */
    private static function getCacheInstance(): \Cleup\Cache\Cache
    {
        if (self::$cacheInstance === null) {
            $storagePath = Registry::get('cachePath', Registry::OPTIONS, '/tmp/cache');
            $defaultTtl = Registry::get('cacheTtl', Registry::OPTIONS, 3600);

            $driver = (new LocalDriver())
                ->storagePath($storagePath)
                ->defaultTtl($defaultTtl);

            self::$cacheInstance = (new \Cleup\Cache\Cache($driver))
                ->namespace('configuration');
        }

        return self::$cacheInstance;
    }

    /**
     * If the cache is used
     * 
     * @return bool
     */
    public static function has()
    {
        return !!Registry::get('cache', Registry::OPTIONS, false);
    }

    /**
     * If the cache exists
     * 
     * @return bool
     */
    public static function exists()
    {
        return self::getCacheInstance()->has('configuration');
    }

    /**
     * Path to the output cache file (for compatibility)
     * 
     * @return string
     */
    public static function output()
    {
        $path = Registry::get('cachePath', Registry::OPTIONS, '/tmp/cache');
        return $path . '/cache.config.php';
    }

    /**
     * Load the configuration from cache
     * 
     * @return void
     */
    public static function load()
    {
        $config = self::getCacheInstance()->get('configuration');

        if ($config && is_array($config)) {
            Registry::preset(
                $config[Registry::OPTIONS] ?? [],
                $config[Registry::CONFIG] ?? [],
                $config[Registry::ENV] ?? []
            );
        }
    }

    /**
     * Create a configuration cache
     * 
     * @return bool
     */
    public static function create()
    {
        $config = Registry::getAll();
        $cacheTtl = Registry::get('cacheTtl', Registry::OPTIONS, 3600);

        return self::getCacheInstance()->set('configuration', $config, $cacheTtl);
    }

    /**
     * Clear configuration cache
     * 
     * @return bool
     */
    public static function clear()
    {
        return self::getCacheInstance()->delete('configuration');
    }

    /**
     * Get cache storage path
     * 
     * @return string
     */
    public static function getStoragePath()
    {
        return self::getCacheInstance()->getDriver()->getStoragePath();
    }
}

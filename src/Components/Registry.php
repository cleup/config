<?php

namespace Cleup\Core\Configuration\Components;

use Cleup\Components\StoreManager\Store;

class Registry
{
    /**
     *  Root name
     * 
     * @var string
     */
    public const ROOT = '__CONFIGURATION_REGISTRY__';

    /**
     *  Options Context name
     * 
     * @var string
     */
    public const OPTIONS = '__OPTIONS__';

    /**
     * Configuration context name
     * 
     * @var string
     */
    public const CONFIG = '__CONFIG__';

    /**
     * Environment context name
     * 
     * @var string
     */
    public const ENV = '__ENV__';

    /**
     * Preset configuration data
     * 
     * @param array $options
     * @param array $config
     * @param array $env
     * @return void
     */
    public static function preset(
        $options = array(),
        $config = array(),
        $env = array()
    ) {
        Store::set(static::ROOT, [
            static::OPTIONS => $options,
            static::CONFIG => $config,
            static::ENV => $env,
        ]);
    }

    /**
     * Creating a route with dot syntax
     * 
     * @param string $type
     * @param string|bool $key
     * @return string
     */
    private static function dotRoute($type, $key = false)
    {
        return static::ROOT . '.' . $type . (!empty($key) ? '.' .  $key : '');
    }

    /**
     * Set the configurations in the registry
     * 
     * @param string|int $key
     * @param mixed $value 
     * @param string $type 
     * @return void
     */
    public static function set($key, $value, $type)
    {
        Store::set(
            static::dotRoute($type, $key),
            $value
        );
    }

    /**
     * Get the configuration from the registry
     * 
     * @param string|int $key
     * @param string $type
     * @param mixed $default
     * @return mixed 
     */
    public static function get($key, $type, $default = null)
    {
        return Store::get(
            static::dotRoute($type, $key),
            $default
        );
    }

    /**
     * Get all configuration data from the registry
     * 
     * @return array
     */
    public static function getAll()
    {
        return Store::get(static::ROOT);
    }
}

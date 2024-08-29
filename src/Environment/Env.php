<?php

namespace Cleup\Core\Configuration\Environment;

use Cleup\Core\Configuration\Components\Registry;
use Cleup\Helpers\Arr;

class Env
{

    /**
     * Make changes to the environment
     * 
     * @return void
     */
    public static function write()
    {
        Arr::map(
            Registry::get(
                false,
                Registry::ENV
            ),
            function ($value, $key) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                putenv("{$key}={$value}");
            }
        );
    }

    /**
     * Find the .env file
     * 
     * @return string
     */
    public static function find()
    {
        $filePath = '';

        $types = Registry::get(
            'envTypes',
            Registry::OPTIONS
        );

        $path =  Registry::get(
            'envPath',
            Registry::OPTIONS
        );

        $isDev = Registry::get(
            'debug',
            Registry::OPTIONS
        );

        $type =  $types[$isDev ? "local" : "production"];

        if (is_array($type)) {
            $name = array_search(min($type), $type);

            if (file_exists($path . '.env.' . $name))
                $filePath = $path . '.env.' . $name;
        }

        if (empty($filePath) && file_exists($path . '.env'))
            $filePath = $path . '.env';

        return $filePath;
    }

    /**
     * Load the environment file
     * 
     * @return void
     */
    public static function load(): void
    {
        $path = static::find();
        $parser = new Parser();

        Registry::set(
            false,
            $parser->parse($path),
            Registry::ENV
        );
    }

    /**
     * Get the value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = '', $default = null)
    {
        return Registry::get(
            $key,
            Registry::ENV,
            $default
        );
    }

    /**
     * Set the value
     * 
     * @param string|bool $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        Registry::set(
            $key,
            $value,
            Registry::ENV
        );
    }
}

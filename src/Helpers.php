<?php

use Cleup\Core\Configuration\Components\Registry;
use Cleup\Core\Configuration\Config;
use Cleup\Core\Configuration\Environment\Env;

if (!function_exists('config_path')) {

    /**
     * Get the path to the configuration
     * 
     * @return string 
     */
    function config_path()
    {
        return Registry::get(
            'configPath',
            Registry::OPTIONS
        );
    }
}

if (!function_exists('config')) {

    /**
     * Get configuration data
     * 
     * @param string|int $key
     * @param mixed $default
     * @return mixed 
     */
    function config($key, $default = null)
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('env_path')) {

    /**
     * Get the path to the environment directory
     * 
     * @return string 
     */
    function env_path()
    {
        return Registry::get(
            'envPath',
            Registry::OPTIONS
        );
    }
}

if (!function_exists('env')) {

    /**
     * Get env data
     * 
     * @param string|int $key
     * @param mixed $default
     * @return mixed 
     */
    function env($key, $default = null)
    {
        return Env::get($key, $default);
    }
}

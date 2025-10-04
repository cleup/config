<?php

namespace Cleup\Configuration\Environment;

use Cleup\Configuration\Components\Registry;

class Env
{
    private static ?Parser $parser = null;

    /**
     * Get parser instance
     * 
     * @return Parser
     */
    private static function getParser(): Parser
    {
        if (self::$parser === null) {
            self::$parser = new Parser();
        }
        return self::$parser;
    }

    /**
     * Make changes to the environment
     * 
     * @return void
     */
    public static function write()
    {
        $env = Registry::get(false, Registry::ENV);

        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    /**
     * Find the .env file
     * 
     * @return string
     */
    public static function find()
    {
        $filePath = '';

        $types = Registry::get('envTypes', Registry::OPTIONS);
        $path = Registry::get('envPath', Registry::OPTIONS);
        $isDev = Registry::get('debug', Registry::OPTIONS);

        $type = $types[$isDev ? "local" : "production"];

        if (is_array($type)) {
            $name = array_search(min($type), $type);

            if (file_exists($path . '.env.' . $name)) {
                $filePath = $path . '.env.' . $name;
            }
        }

        if (empty($filePath) && file_exists($path . '.env')) {
            $filePath = $path . '.env';
        }

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
        
        if (empty($path)) {
            return;
        }

        $parser = self::getParser();
        Registry::set(false, $parser->parse($path), Registry::ENV);
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
        return Registry::get($key, Registry::ENV, $default);
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
        Registry::set($key, $value, Registry::ENV);
    }

    /**
     * Check if environment variable exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key): bool
    {
        return Registry::get($key, Registry::ENV) !== null;
    }

    /**
     * Get all environment variables
     * 
     * @return array
     */
    public static function all(): array
    {
        return Registry::get(false, Registry::ENV, []);
    }
}
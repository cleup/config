<?php

namespace Cleup\Configuration;

use Cleup\Configuration\Components\Registry;

class Config
{
    /**
     * Load all configuration files
     * 
     * @param string $path
     * @return void
     */
    public static function load($path = '')
    {
        $configPath = Registry::get('configPath', Registry::OPTIONS);

        $scan = array_diff(
            scandir($configPath . $path),
            array('.', '..')
        );

        foreach ($scan as $file) {
            $fullPath = $configPath . $path . $file;

            if (is_dir($fullPath) && $fullPath !== $configPath) {
                static::load($path . $file . '/');
            } else {
                $php = explode('.', $file);

                if (!empty($php) && is_array($php) && end($php) === 'php') {
                    $fileName = $php[0];
                    $config = require_once($fullPath);
                    if (is_array($config)) {
                        $dot = '';

                        if (!empty($path)) {
                            $dot = str_replace('/', '.', rtrim($path . $file, '.php'));
                        }

                        static::set(($dot ? $dot : $fileName), $config);
                    }
                }
            }
        }
    }

    /**
     * Get configuration data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = '', $default = null)
    {
        return Registry::get($key, Registry::CONFIG, $default);
    }

    /**
     * Set configuration data
     * 
     * @param string|bool $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        Registry::set($key, $value, Registry::CONFIG);
    }

    /**
     * Check if configuration exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key): bool
    {
        return Registry::get($key, Registry::CONFIG) !== null;
    }

    /**
     * Get all configuration
     * 
     * @return array
     */
    public static function all(): array
    {
        return Registry::get(false, Registry::CONFIG, []);
    }

    /**
     * Merge configuration
     * 
     * @param array $config
     * @return void
     */
    public static function merge(array $config): void
    {
        $existing = self::all();
        $merged = array_merge_recursive($existing, $config);
        self::set('', $merged);
    }
}
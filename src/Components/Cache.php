<?php

namespace Cleup\Core\Configuration\Components;

use Cleup\Helpers\Arr;

class Cache
{
    /**
     * If the cache is used
     * 
     * @return bool
     */
    public function has()
    {
        return !!Registry::get(
            'cache',
            Registry::OPTIONS
        );
    }

    /**
     * If the cache file exists
     * 
     * @return bool
     */
    public function exists()
    {
        return !!file_exists($this->output());
    }

    /**
     * Path to the output cache file
     * 
     * @return string
     */
    public function output()
    {
        $path = Registry::get(
            'cachePath',
            Registry::OPTIONS
        );

        return $path . 'cache.config.php';
    }

    /**
     * Load the configuration cache file
     * 
     * @return void
     */
    public function load()
    {
        $config = array();

        if ($this->exists()) {
            $config = require_once($this->output());

            Registry::preset(
                $config[Registry::OPTIONS],
                $config[Registry::CONFIG],
                $config[Registry::ENV]
            );
        }
    }

    /**
     * Create a configuration cache file
     * 
     * @return bool
     */
    public function create()
    {
        return Arr::write(
            Registry::getAll(),
            $this->output(),
            [
                'debug' => Registry::get(
                    'debug',
                    Registry::OPTIONS
                ),
                'comment' => PHP_EOL .
                    "\tThe current file is generated automatically," . PHP_EOL .
                    "\tdo not make changes to it as they will be lost." . PHP_EOL .
                    "\tDelete the file and it will be recreated according to your configuration."
                    . PHP_EOL
            ]
        );
    }
}

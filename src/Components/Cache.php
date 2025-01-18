<?php

namespace Cleup\Configuration\Components;

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
        $content = '<?php' . PHP_EOL;
        $content .= PHP_EOL .
            "/*" . PHP_EOL .
            "\tThe current file is generated automatically," . PHP_EOL .
            "\tdo not make changes to it as they will be lost." . PHP_EOL .
            "\tDelete the file and it will be recreated according to your configuration." . PHP_EOL .
            "*/"  . PHP_EOL;
        $content .= 'return ';
        $content .= var_export(
            Registry::getAll(),
            true
        );
        $content .= ';';
        $file = $this->output();
        $directory = dirname($file);

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0775, true)) {
                throw new \Exception('Failed to create directory: ' . $directory);
            }
        }

        if (!@file_put_contents($file, $content)) {
            throw new \Exception('Failed to create file: ' . $$file);
        }
        
        return $content;
    }
}

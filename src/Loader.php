<?php

namespace Cleup\Configuration;

use Cleup\Configuration\Components\Registry;
use Cleup\Configuration\Components\Cache;
use Cleup\Configuration\Environment\Env;

class Loader
{
    /**
     * Default options
     * 
     * @var array
     */
    private $options = array(
        'cache' => true,
        'cachePath' => '/tmp/cache',
        'cacheTtl' => 0,
        'configPath' => '',
        'env' => true,
        'envPath' => '',
        'debug' => false,
        'envTypes' => array(
            'production' => [
                'production' => 100
            ],
            'local' => [
                'local' => 100,
                'dev' => 200
            ]
        )
    );

    /**
     * Launch
     * 
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = array_merge(
            $this->options,
            $options
        );

        $this->filterPath('configPath', 'envPath', 'cachePath');
        Registry::preset($this->options);
    }

    /**
     * Filter path
     * 
     * @param string [$names]
     */
    private function filterPath(...$names)
    {
        foreach ($names as $name) {
            $this->options[$name] = rtrim(rtrim($this->options[$name], '\\')) . '/';
        }
    }

    /**
     * Scan the configuration
     * 
     * @return void
     */
    private function scan()
    {
        Env::load();
        Config::load();
    }

    /**
     * Load the entire configuration
     * 
     * @return void
     */
    public function load()
    {
        if ($this->options['cache']) {
            if (Cache::has()) {
                if (Cache::exists()) {
                    Cache::load();
                } else {
                    $this->scan();
                    if (Cache::create()) {
                        Cache::load();
                    }
                }
            } else {
                $this->scan();
            }
        } else {
            $this->scan();
        }

        Env::write();
    }

    /**
     * Reload configuration (bypass cache)
     * 
     * @return void
     */
    public function reload()
    {
        Cache::clear();
        $this->scan();
        Env::write();
    }
}
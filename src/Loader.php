<?php

namespace Cleup\Core\Configuration;

use Cleup\Core\Configuration\Components\Cache;
use Cleup\Core\Configuration\Components\Registry;
use Cleup\Core\Configuration\Environment\Env;

class Loader
{
    /**
     * Default options
     * 
     * @var array
     */
    private $options = array(
        'cache' => true,
        'cachePath' => '',
        'configPath' => '',
        'env' => true,
        'envPath' => '',
        'debug' => false,
        'envTypes' => array(
            'default' => '',
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

        $this->filterPath(
            'configPath',
            'envPath',
            'cachePath'
        );

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
            $this->options[$name] = rtrim(
                rtrim($this->options[$name], '\\')
            ) . '/';
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
            $cache = new Cache();

            if ($cache->has()) {
                if ($cache->exists())
                    $cache->load();
                else {
                    $this->scan();

                    if ($cache->create())
                        $cache->load();
                }
            } else
                $this->scan();
        } else
            $this->scan();

        Env::write();
    }
}

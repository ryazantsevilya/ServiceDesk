<?php


namespace App\Providers;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo;

use Phalcon\Di\ServiceProviderInterface;
use RuntimeException;

class DbProvider implements ServiceProviderInterface
{
    protected $providerName = 'db';

    protected $adapters = [
        'pgsql'  => Pdo\Postgresql::class
    ];

    public function register(\Phalcon\DiInterface $di)
    {
        /** @var Config $config */
        $config = $di->getShared('config')->get('database');
        $class  = $this->getClass($config);
        $config = $this->createConfig($config);

        $di->set($this->providerName, function () use ($class, $config) {
            return new $class($config);
        });
    }

    private function getClass(Config $config): string
    {
        $name = $config->get('adapter', 'Unknown');

        if (empty($this->adapters[$name])) {
            throw new RuntimeException(
                sprintf(
                    'Adapter "%s" has not been registered',
                    $name
                )
            );
        }

        return $this->adapters[$name];
    }

    private function createConfig(Config $config): array
    {
        $dbConfig = $config->toArray();
        unset($dbConfig['adapter']);

        $name = $config->get('adapter');
        switch ($this->adapters[$name]) {
            case Pdo\Postgresql::class:
                unset($dbConfig['charset']);
                break;
        }

        return $dbConfig;
    }

}
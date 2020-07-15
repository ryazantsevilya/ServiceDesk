<?php


namespace App\Providers;


use App\Application;
use Phalcon\Config;
use Phalcon\Di\ServiceProviderInterface;

class ConfigProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $providerName = 'config';

    public function register(\Phalcon\DiInterface $di)
    {
        $application = $di->getShared(Application::APPLICATION_PROVIDER);
        /** @var string $rootPath */
        $rootPath = $application->getRootPath();
        $di->setShared($this->providerName, function () use ($rootPath) {
            $config = include $rootPath . '/app/config/config.php';

            return new Config($config);
        });
    }
}
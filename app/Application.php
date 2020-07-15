<?php


namespace App;


use App\Controllers\Helpers\StatusEnum;
use App\Controllers\TicketsController;
use Exception;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection as MicroCollection;

class Application
{
    const APPLICATION_PROVIDER = 'bootstrap';

    /**
     * @var MvcApplication
     */
    protected $app;

    /**
     * @var DiInterface
     */
    protected $di;

    /**
     * Project root path
     *
     * @var string
     */
    protected $rootPath;


    public function __construct($rootPath)
    {
        $this->di = new FactoryDefault();
        $this->app = $this->createApplication();
        $this->rootPath = $rootPath;

        $this->di->setShared(self::APPLICATION_PROVIDER, $this);

        $this->initializeProviders();
    }


    public function run() : void
    {
        $this->registerRouterCollections();

        $this->disableCORS();

        $this->app->error(
            function ($exception) {
                $this->app->response->setStatusCode(500)->send();
                echo json_encode(
                    [
                        'code'    => $exception->getCode(),
                        'status'  => StatusEnum::ERROR,
                        'message' => $exception->getMessage(),
                    ]
                );
            }
        );

        $this->app->notFound(
            function () {
                $this->app->response->setStatusCode(404);
                $this->app->response->sendHeaders();
            }
        );


        $this->app->handle();
    }

    private function disableCORS(){
        // CORS Disable
        $this->app->before(
            function () {
                $origin = $this->app->request->getHeader('ORIGIN') ? $this->app->request->getHeader('ORIGIN') : '*';

                if (strtoupper($this->app->request->getMethod()) == 'OPTIONS') {
                    $this->app->response
                        ->setHeader('Access-Control-Allow-Origin', $origin)
                        ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
                        ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
                        ->setHeader('Access-Control-Allow-Credentials', 'true');

                    $this->app->response->setStatusCode(200, 'OK')->send();

                    exit;
                }

                $this->app->response
                    ->setHeader('Access-Control-Allow-Origin', $origin)
                    ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
                    ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
                    ->setHeader('Access-Control-Allow-Credentials', 'true');

                return true;
            });
        $this->app->options('/{catch:(.*)}', function() {
            $this->app->response->setStatusCode(200)->send();
        });
    }

    private function registerRouterCollections(): void
    {
        $tickets = new MicroCollection();

        $tickets->setHandler(new TicketsController());

        $tickets->setPrefix('/tickets');

        $tickets->get('/', 'index');
        $tickets->get('/{id:[0-9]+}', 'findAction');
        $tickets->post('/','createAction');
        $tickets->delete('/{id:[0-9]+}','deleteAction');
        $tickets->patch('/{id:[0-9]+}','updateAction');
        $this->app->mount($tickets);
    }

    protected function createApplication()
    {
        return new Micro($this->di);
    }

    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    protected function initializeProviders(): void
    {
        $filename = $this->rootPath . '/app/config/providers.php';
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception('File providers.php does not exist or is not readable.');
        }

        $providers = include_once $filename;
        foreach ($providers as $providerClass) {
            /** @var ServiceProviderInterface $provider */
            $provider = new $providerClass;
            $provider->register($this->di);
        }
    }
}
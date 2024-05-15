<?php

use App\Config;
use App\Entity\User;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use function DI\create;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create Container using PHP-DI
$container = new Container();

// $container->set(Config::class, fn() => new Config($_ENV));
// $container->set(EntityManager::class, fn(Config $config) => EntityManager::create(
//     $config->db,
//     ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../app/Entity'])
// ));
// // Set container to create App with on AppFactory
// AppFactory::setContainer($container);

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger  
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});


$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/users', function (Request $request, Response $response, $args) {
    
    // $users = [
    //     [
    //         'id' => 1,
    //         'name' => 'John Doe',
    //     ],
    //     [
    //         'id' => 2,
    //         'name' => 'Jane Doe',
    //     ]
    // ];

    $users = $this->get(User::class)->findAll();

    var_dump($users);
    die;

    $payload = json_encode($users);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// Run app
$app->run();

<?php

/**
 * @license Apache 2.0
 */

define('DIR_ROOT', dirname(__FILE__));
require_once DIR_ROOT.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php'; 
require_once DIR_ROOT.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'inc.config.php';

use DataSearchEngine\Lib\SolrConsumer;
use DataSearchEngine\Lib\SireneConsumer;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use DataSearchEngine\Middleware\EntryMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use DataSearchEngine\Middleware\AuthenticationMiddleware;

// Initialize data search engine application
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

// Container initialization
$responseFactory = $app->getResponseFactory();

// Add CSRF
//$container->set('csrf', function () use ($responseFactory) {
//    $guard = new Guard($responseFactory);
//    $guard->setPersistentTokenMode(true);
//    $guard->setFailureHandler(function (Request $request, RequestHandler $handler) {
//        throw new HttpBadRequestException($request);
//    });
//    return $guard;
//});
$container->set('view', function() {
    if (!DEVELOPMENT) {
        return Twig::create('templates', ['cache' => 'templates/cache']);
    } else {
        return Twig::create('templates');
    }
});
$container->set('flash', function () {
    return new Messages();
});
$container->set('errorHandler', function () {
    return function ($request, $response, $exception) use ($container) {
        return $response->withHeader('Location', '500.php');
    };
});
$container->set('solr', function () {
    return new SolrConsumer();
});
$container->set('sirene', function () {
    return new SireneConsumer();
});
$container->set('user', function() {
    return null;
});

$app->add(TwigMiddleware::createFromContainer($app));
$app->add(new EntryMiddleware($container));

// Routing
$app->group('', function (RouteCollectorProxy $group) use ($app) {
    $group->get('/',                          \DataSearchEngine\Controller\View\HomeViewController::class);
    $group->post('/action/search',            \DataSearchEngine\Controller\Action\SearchAction::class.':search');
    $group->post('/action/advanced-search',   \DataSearchEngine\Controller\Action\AdvancedSearchAction::class.':search');
    $group->post('/action/explore-directory', \DataSearchEngine\Controller\Action\ExploreDirectoryAction::class.':explore');
});

// Define Custom Error Handler
$errorHandler = function (Request $request, Throwable $exception,
bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($app) {
    // display custom error page
    $request = $request->withAttribute('exception', $exception);
    return $app->get($request->getUri()->getPath(), DataSearchEngine\Controller\View\ErrorViewController::class)->run($request);
};
$errorMiddleware = $app->addErrorMiddleware(DEVELOPMENT, LOGGING, DEBUG);
if (PRODUCTION) {
    $errorMiddleware->setDefaultErrorHandler($errorHandler);
}

$app->run();

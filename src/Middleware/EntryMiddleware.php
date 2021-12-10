<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Middleware to initialize session and Flash messages.
 *
 * @package DataSearchEngine\Middleware
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class EntryMiddleware {

    protected $container;

    public function __construct(ContainerInterface $container) {
        session_start();
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $this->container->get('view')->offsetSet('flash', $this->container->get('flash'));

        if ($this->container->get('user') == null) {
            $this->container->set('user', function() {
                if (isset($_SESSION['user'])) {
                    return unserialize($_SESSION['user']);
                } else {
                    return null;
                }
            });
        }
        
        return $handler->handle($request);
    }
}
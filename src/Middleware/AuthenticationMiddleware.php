<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

/**
 * Middleware to check swagger user authentication.
 *
 * @package DataSearchEngine\Middleware
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class AuthenticationMiddleware {

    protected $groups;

    public function __construct($groups = null) {
        $this->groups = $groups;
    }

    public function __invoke(Request $request, RequestHandler $handler) : Response {
        $user = null;
        if (isset($_SESSION['user'])) {
            $user = unserialize($_SESSION['user']);
        }
        if ($user == null) {
            throw new HttpUnauthorizedException($request);
        }
        return $handler->handle($request);
    }
}
<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\View;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Classe HomeViewController
 *
 * @package DataSearchEngine\Controller\View
 * @author  Alexis ZUCHER <a.zucher@girondenumerique.fr>
*/
final class HomeViewController extends ViewController {

    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args) : ResponseInterface {
        return $this->twig->render($response, 'accueil.html', [
            'currentUrl'        => $request->getUri()->getPath()
        ]);
    }
}
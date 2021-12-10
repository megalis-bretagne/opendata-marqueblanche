<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\View;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Classe AdministrationViewController
 *
 * @package DataSearchEngine\Controller\View
 * @author  Alexis ZUCHER <a.zucher@girondenumerique.fr>
*/
final class AdministrationViewController extends ViewController {

    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args) : ResponseInterface {
        $user = $this->container->get('user');
        if ($user != null) {
            return $this->twig->render($response, 'administration.html', [
                'currentUrl'        => $request->getUri()->getPath()
            ]);
        } else {
            return $this->twig->render($response, 'connexion.html', [
                'currentUrl'        => $request->getUri()->getPath()
            ]);
        }
    }
}
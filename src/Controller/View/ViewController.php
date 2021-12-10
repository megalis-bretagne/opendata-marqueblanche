<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\View;

use Psr\Container\ContainerInterface;

/**
 * ViewController interface to load page's commons objects
 *
 * @package Reporting\Controller\View
 * @author  Alexis ZUCHER <a.zucher@girondenumerique.fr>
 */
class ViewController {

    protected $container;

    protected $csrf;

    protected $flash;

    protected $twig;

    protected $user;

    public function __construct(ContainerInterface $container) {
        // Slim container
        $this->container = $container;

        // CSRF guard
        //$this->csrf = $this->container->get('csrf');

        // Flash messages
        $this->flash = $this->container->get('flash');

        // Twig view
        $this->twig = $this->container->get('view');

        // Authenticated user
        $this->user = $this->container->get('user');
        $this->twig->getEnvironment()->addGlobal('user', $this->user);

        // CSRF tokens
//        $csrfArray = array(
//            'keys' => ['name' => $this->csrf->getTokenNameKey(), 'value' => $this->csrf->getTokenValueKey()],
//            'name'  => $this->csrf->getTokenName(),
//            'value' => $this->csrf->getTokenValue()
//        );
//        $this->twig->getEnvironment()->addGlobal('csrf', $csrfArray);

        // General parameters
        $this->twig->getEnvironment()->addGlobal('adminName', ADMIN_NAME);
        $this->twig->getEnvironment()->addGlobal('adminEmail', ADMIN_MAIL);
    }
}

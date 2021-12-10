<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\Action;

use Psr\Container\ContainerInterface;

/**
 * ActionController interface class to load commons object for action controllers.
 *
 * @package DataSearchEngine\Controller\Action
 * @author  Alexis ZUCHER <a.zucher@girondenumerique.fr>
*/
class ActionController {

    protected $container;

    protected $flash;

    protected $user;

    public function __construct(ContainerInterface $container) {
        // Slim container
        $this->container = $container;

        // Flash messages
        $this->flash = $this->container->get('flash');

        // Authenticated user
        $this->user = $this->container->get('user');
    }
}
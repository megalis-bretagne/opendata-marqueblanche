<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\Action;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DataSearchEngine\Utils\ArrayUtils;

/**
 * Authentication action without LDAP with user settings in the configuration file. 
 * Delete other AuthenticationAction class and rename this file AuthenticationAction.php
 *
 * @package DataSearchEngine\Action
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
final class AuthenticationAction extends ActionController {

     protected $container;

     public function __construct(ContainerInterface $container) {
          parent::__construct($container);
     }

     public function login(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
          $params = $request->getParsedBody();
          if ($params != null && sizeof($params) > 0) {
               $login         = ArrayUtils::get($params, 'email');
               $password      = ArrayUtils::get($params, 'password');
               
               if ($login == ADMIN_LOGIN && $password == ADMIN_PASSWORD) {
                    $user = array(
                              "name"    => ADMIN_NAME,
                              "email"   => ADMIN_MAIL
                         );
                    $_SESSION['user'] = \serialize($user);
                    return $response->withHeader('Location', '/administration');
               } else {
                    $this->flash->addMessage('error', 'Adresse email ou mot de passe erron&eacute;');
               }
          }
          return $response->withHeader('Location', '/administration');
     }

     public function logout(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
          session_unset();
          session_destroy();
          return $response->withHeader('Location', '/administration');
     }

}
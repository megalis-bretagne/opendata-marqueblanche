<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\View;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Classe ErrorViewController to display custom error page
 *
 * @package DataSearchEngine\Controller\View
 * @author  Alexis ZUCHER <a.zucher@girondenumerique.fr>
*/
final class ErrorViewController extends ViewController {

    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args) : ResponseInterface {
        $exception = $request->getAttribute('exception');
        switch ($exception->getCode()) {
            case 400:
                $code = 400;
                $message = 'La syntaxe de la requ&ecirc;te est mal formul&eacute;e ou est impossible &agrave; satisfaire.';
                break;
            case 401:
                $code = 401;
                $message = 'Vous n\'&ecirc;tes pas autoris&eacute; &agrave; acc&eacute;der &agrave; cette page.';
                break;
            case 403:
                $code = 403;
                $message = 'Vous n\'&ecirc;tes pas autoris&eacute; &agrave; effectuer cette op&eacute;ration.';
                break;
            case 404:
                $code = 404;
                $message = 'La page demand&eacute;e n\'a pas &eacute;t&eacute; trouv&eacute;e car elle a peut-&ecirc;tre &eacute;t&eacute; d&eacute;plac&eacute;e, ou elle n\'existe plus.';
                break;
            case 405:
                $code = 405;
                $message = 'La m&eacute;thode utilis&eacute;e pour cette requ&ecirc;te n\'est pas support&eacute;e par la ressource cibl&eacute;e.';
                break;
            case 500:
                $code = 500;
                $message = 'Erreur interne, le serveur a rencontr&eacute; une condition inattendue qui l\'a emp&ecirc;ch&eacute; de satisfaire la demande.';
                break;
            case 504:
                $code = 504;
                $message = 'La passerelle met trop de temps &agrave; r&eacute;pondre.';
                $break;
            default:
                $code = 500;
                $message = 'Erreur interne, le serveur a rencontr&eacute; une condition inattendue qui l\'a emp&ecirc;ch&eacute; de satisfaire la demande.<br /><br /><i>'.$exception->getMessage().'</i>';
        }

        return $this->twig->render($response, 'erreur.html', [
            'currentUrl'        => $request->getUri()->getPath(),
            'errorCode'         => $code,
            'errorMessage'      => $message
        ]);
    }
}
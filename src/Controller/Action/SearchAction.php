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
 * Simple search action.
 *
 * @package DataSearchEngine\Action
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class SearchAction {

     protected $container;

     public function __construct(ContainerInterface $container) {
          $this->container = $container;
     }

     public function search(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
          $total = 0;
          $params = $request->getParsedBody();
          $response->withHeader('Content-Type', 'application/json');
          if ($params != null && sizeof($params) > 0) {
              $keywords  = ArrayUtils::get($params, 'search-field');
              $siren     = ArrayUtils::get($params, 'siren');
              $offset    = ArrayUtils::get($params, 'offset');
              $limit     = ArrayUtils::get($params, 'limit');

              $solr = $this->container->get('solr');

              // Plain text search.
              $search = $solr->simpleSearch($keywords, $siren, $offset, $limit);
              $total = $search['response']['numFound'];
              if ($total == 0) {
                   // Seconde chance with query filter search.
                   $search = $solr->simpleSearch($keywords, $siren, $offset, $limit, true);
                   $total = $search['response']['numFound'];
              }

              $response->getBody()->write(json_encode($search['response']));
          }
          
          if ($total == 0) {
               return $response->withStatus(204);
          } else if (sizeof($search['response']['docs']) < $total) {
               return $response->withStatus(206);
          } else {
               return $response->withStatus(200);
          }
     }

}

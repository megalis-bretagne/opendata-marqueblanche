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
 * Delete document action.
 *
 * @package DataSearchEngine\Action
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class DeleteDocumentAction {

     protected $container;

     public function __construct(ContainerInterface $container) {
          $this->container = $container;
     }

     public function delete(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
          $params = $request->getParsedBody();
          if ($params != null && sizeof($params) > 0) {
               $documentUrl   = ArrayUtils::get($params, 'documentUrl');
               $documentId    = ArrayUtils::get($params, 'documentId');
               
               $solr = $this->container->get('solr');
               if (strpos($documentUrl, $_SERVER['HTTP_HOST']) !== false) {
                    $path = substr($documentUrl, strpos($documentUrl, '/OpenData') + 10);
                    if (file_exists(DIR_ROOT.'/OpenData//'.$path)) {
                         if (unlink(DIR_ROOT.'/OpenData//'.$path)) {
                              return $this->deleteDocumentIndex($response, $documentId);
                         } else {
                              $response->getBody()->write('Une erreur est survenue lors de la suppression du document');
                              return $response->withStatus(500);
                         }
                    } else {
                         $message = 'Le document '.$path.' n\'a pas &eacute;t&eacute; trouv&eacute; sur le serveur.<br />';
                         $this->deleteDocumentIndex($response, $documentId, $message);
                    }
               } else {
                    $response->getBody()->write($message);
                    return $response->withStatus(404);
               }
          }
     }

     /**
      * Call SolrConsumer to delete a document index
      *
      * @param ResponseInterface $response Response object from Slim routing
      * @param string $documentId Solr document id
      * @param string $message (optional) Optional message
      * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
      */
     private function deleteDocumentIndex(ResponseInterface $response, string $documentId, $message = '') : ResponseInterface {
          $solr = $this->container->get('solr');
          try {
               if ($solr->deleteDocumentIndexById($documentId)) {
                    $response->getBody()->write($message.'Le document a bien &eacute;t&eacute; supprim&eacute;');
                    return $response->withStatus(200);
               } else {
                    $response->getBody()->write('Une erreur est survenue lors de la suppression de l\'index du document');
                    return $response->withStatus(500);
               }        
          } catch (\Exception $e) {
               $response->getBody()->write($e->getMessage());
               return $response->withStatus(500);
          }
     }

}
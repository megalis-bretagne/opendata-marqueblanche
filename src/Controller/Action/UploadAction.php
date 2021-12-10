<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Controller\Action;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DataSearchEngine\Utils\ArrayUtils;
use DataSearchEngine\Utils\NormalizeString;
use DataSearchEngine\Entity\DocumentFile;
use DataSearchEngine\Entity\CitizenDocument;

/**
 * Admin upload action.
 *
 * @package DataSearchEngine\Action
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
final class UploadAction {

     protected $container;

     public function __construct(ContainerInterface $container) {
          $this->container = $container;
     }

     public function upload(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
          $params = $request->getParsedBody();
          $response->withHeader('Content-Type', 'application/json');
          if ($params != null && sizeof($params) > 0) {
               $siren         = ArrayUtils::get($params, 'siren');
               $category      = ArrayUtils::get($params, 'category');
               $description   = ArrayUtils::get($params, 'description');

               $sirene = $this->container->get('sirene');
               $solr = $this->container->get('solr');
               try {
                    if (!isset($_SESSION['collectivite'])) {
                         $collectivite = $sirene->getOrganizationInformations($siren);
                         if ($collectivite != null) {
                              $_SESSION['collectivite'] = \serialize($collectivite);
                         }
                    } else {
                         $collectivite = \unserialize($_SESSION['collectivite']);
                    }
               } catch (\Exception $e) {
                    $response->getBody()->write($e->getMessage());
                    return $response->withStatus(500);
               }

               if ($collectivite != null) {
                    $destinationDirectory = '/OpenData/'.$this->getDestinationDirectory($category).'/'.date('Y');
                    if (!is_dir(DIR_ROOT.$destinationDirectory)) {
                         mkdir(DIR_ROOT.$destinationDirectory, 0755);
                    }
                    $filename = NormalizeString::normalize($_FILES['file']['name']);
                    $filepath = $destinationDirectory.'/'.$filename;
                    if(move_uploaded_file($_FILES['file']['tmp_name'], DIR_ROOT.$filepath)) {
                         $files = array();
                         $documentFile = new DocumentFile($filename, DIR_ROOT.$filepath);
                         $documentFile->setOpenDataUrl('https://'.$_SERVER['SERVER_NAME'].$filepath);
                         array_push($files, $documentFile);
                         $document = new CitizenDocument($description, $destinationDirectory, $files);
                         $document->setOrigin('publication_administration');
                         $document->setCollectivite($collectivite);
     
                         // Document indexation on Solr server
                         try {
                              if (!$solr->documentIsAlreadyIndexed($document->getCollectivite()->getSiren(), $documentFile->getHash())) {
                                   if ($solr->indexDocumentFile($document, $documentFile)) {
                                        $response->getBody()->rewind();
                                        $response->getBody()->write('Publication effectu&eacute;e');
                                        return $response->withStatus(200);
                                   } else {
                                        unlink(DIR_ROOT.$filepath);
                                        $response->getBody()->write('Le fichier n\a pu &ecirc;tre index&eacute;');
                                        return $response->withStatus(500);
                                   }
                              } else {
                                   unlink(DIR_ROOT.$filepath);
                                   $response->getBody()->write('Ce fichier a d&eacute;j&agrave; &eacute;t&eacute; publi&eacute;');
                                   return $response->withStatus(200);
                              }
                         } catch (Exception $e) {
                              $response->getBody()->write($e->getMessage());
                              return $response->withStatus(500);
                         }
                    } else {
                         $response->getBody()->write('Erreur lors du t&eacute;l&eacute;chargement du fichier');
                         return $response->withStatus(500);
                    }
               } else {
                    $response->getBody()->write('Impossible de r&eacute;cup&eacute;rer les informations de la collectivit&eacute; pour le SIREN '.$siren);
                    return $response->withStatus(500);
               }
          } else {
               $response->getBody()->write('Les param&eacute;tres saisis ne permettent pas de satisfaire la requ&ecirc;te');
               return $response->withStatus(400);
          }
     }

     /**
      * Get destination directory from id.
      *
      * @param ResponseInterface $response Response object from Slim routing
      * @param integer $category Category id
      * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
      * @return string
      */
     private function getDestinationDirectory($category) {
          $destinationDirectory = null;
          switch ($category) {
               case 0:
                    $destinationDirectory = '0_Actes_administratifs';
                    break;
               case 1:
                    $destinationDirectory = '1_Commande_publique';
                    break;
               case 2:
                    $destinationDirectory = '2_Urbanisme';
                    break;
               case 3:
                    $destinationDirectory = '3_Domaine_et_patrimoine';
                    break;
               case 4:
                    $destinationDirectory = '4_Finances_locales';
                    break;
               case 5:
                    $destinationDirectory = '5_Autres_domaines_de_competences';
                    break;
          }

          return $destinationDirectory;
     }

}
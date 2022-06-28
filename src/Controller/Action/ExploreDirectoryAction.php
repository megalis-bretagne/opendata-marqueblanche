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
 * Explore directory action.
 *
 * @package DataSearchEngine\Action
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class ExploreDirectoryAction {

     protected $container;

     public function __construct(ContainerInterface $container) {
          $this->container = $container;
     }

     public function explore(ServerRequestInterface $request, ResponseInterface $response, $args) : ResponseInterface {
          $nbFile = 0 ;
          $params = $request->getParsedBody();
          if ($params != null && sizeof($params) > 0) {
              $directory  = ArrayUtils::get($params, 'directory');
              $siren  = ArrayUtils::get($params, 'siren');
          }

          $subPath = explode('/', $directory) ;
          $tempPath = '' ;
          $level = 0 ;


         if ($siren != ''){
             $racine = $subPath[0] ."/". $siren;
             $path = array($racine);
             $directory =$racine;

             $i=0;
             foreach ($subPath as $stepFolderSubPath) {
                 if($i>1){
                     $directory = $directory .'/'.$stepFolderSubPath;
                     array_push($path, $stepFolderSubPath);
                 }
                 $i++;
            }
         }else{
             $path = explode('/', $directory) ;
         }

         $pathLength = count($path) ;
      
          $page = '<nav id="breadcrumb" aria-label="breadcrumb" data-toggle="collapse" data-target="#documents-area" aria-expanded="true" aria-controls="documents">
                      <ol class="breadcrumb">' ;
      
          foreach ($path as $stepFolder) {
              if ($stepFolder !== '') { $tempPath .= $stepFolder ; }
              if ($stepFolder !== '..') {
                  if ($level++ == $pathLength - 1) {
                      $page .= '<li class="breadcrumb-item active" aria-current="page">'.$stepFolder.'</li>' ;
                  } else {
                      $page .= '<li class="breadcrumb-item"><a href="'.$tempPath.'">'.$stepFolder.'</a></li>' ;
                  }
              }
              if ($level < $pathLength) { $tempPath .= '/' ; }
          }			
          $page .= '</ol>
                   <i class="fas fa-chevron-down"></i>
                  </nav>' ;

          if (is_dir($directory)) {
               if ($folder = opendir($directory)) {
                    $tabFiles = array() ;
                    while (FALSE !== ($file = readdir($folder))) {
                         if ($file != '.' && $file != '..') {
                              $nbFile ++ ;
                              array_push($tabFiles, $file) ;
                         }
                    }
                    $page .= '<div class="collapse show" id="documents-area">' ;
                    if ($nbFile == 0) { 
                         $page .= '<strong class="font-italic">Ce répertoire est vide.</strong>' ;
                    } else {
                         $page .= '<ul id="documents">' ;
                         // Sort folder or documents by level
                         if ($level == 1) {
                              // Sort by folder asc
                              sort($tabFiles);
                         } else if ($level == 2) {
                              // Sort by date desc
                              rsort($tabFiles);
                         } else {
                             // Sort by date desc
                             rsort($tabFiles);
                              // Sort by creation date asc
//                              $documentsArray = array();
//                              foreach ($tabFiles as $file) {
//                                   $filename = explode('_', $file) ;
//                                   $documentsArray[end($filename)] = $file;
//                              }
//
//                              krsort($documentsArray);
//                              $tabFiles = array_values($documentsArray);
                         }
                         
                         foreach ($tabFiles as $file) {
                              if (is_dir($directory.'/'.$file)) {
                                   // Folder
                                   $page .= '<li class="folder"><a class="link mime folder" href="'.$directory.'/'.$file.'">'.$file.'</a></li>';
                              } else if ($file[0] != '.') {
                                   // File
                                   $page .= '<li class="file"><a class="link mime '.self::getMimeType($file).'" target="_blank" href="'.$directory.'/'.$file.'">'.$file.'</a></li>' ;
                              }
                         }
                         $page .= '</ul>' ;
                    }
                    $page .= '</div><br />';
                  
                    closedir($folder) ;
                    $response->getBody()->write($page);      
               } else {
                    $response->getBody()->write('Le dossier /'.$directory.' n\'a pu &ecirc;tre ouvert.');
                    return $response->withStatus(500);
               }
          } else {
               return $response->withStatus(200);
          }

          return $response;
     }

//     /**
//      * Get usual name for a specific directory.
//      *
//      *
//      * @param string $directory Directory name from filesystem
//      * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
//      * @return string
//      */
//     private static function getDirectoryUsualName($directory) {
//          switch ($directory) {
//               case '0_Actes_administratifs':
//                    $directoryName = 'Actes administratifs';
//                    break;
//               case '1_Commande_publique':
//                    $directoryName = 'Commande publique';
//                    break;
//               case '2_Urbanisme':
//                    $directoryName = 'Urbanisme';
//                    break;
//               case '3_Domaine_et_patrimoine':
//                    $directoryName = 'Domaine et patrimoine';
//                    break;
//               case '4_Finances_locales':
//                    $directoryName = 'Finances locales';
//                    break;
//               case '5_Autres_domaines_de_competences':
//                    $directoryName = 'Autres domaines de comp&eacute;tences';
//                    break;
//               default:
//                    $directoryName = ucfirst($directory);
//          }
//
//          return $directoryName;
//     }
 
     /**
      * Get mime type from filename
      * 
      *
      * @param string $filename File name
      * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
      * @return string
      */
     private static function getMimeType($filename) {
          $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
          if (!(strpos($extension, '.') !== false)) {
               $extension = '.'.$extension ;
          }
          switch ($extension) {
               case '.aac':    // AAC audio
                    $mime ='audio'; break; 
               case '.abw':    // AbiWord document
                    $mime ='base'; break; 
               case '.arc':    // Archive document
                    $mime ='base'; break; 
               case '.avi':    // AVI: Audio Video Interleave
                    $mime ='video'; break; 
               case '.azw':    // Amazon Kindle eBook format
                    $mime ='base'; break; 
               case '.bin':    // Any kind of binary data
                    $mime ='base'; break; 
               case '.bmp':    // Windows OS/2 Bitmap Graphics
                    $mime ='image'; break; 
               case '.bz':     // BZip archive
                    $mime ='base'; break; 
               case '.bz2':    // BZip2 archive
                    $mime ='base'; break; 
               case '.csh':    // C-Shell script
                    $mime ='base'; break; 
               case '.css':    // Cascading Style Sheets (CSS)
                    $mime ='base'; break; 
               case '.csv':    // Comma-separated values (CSV)
                    $mime ='csv'; break; 
               case '.doc':    // Microsoft Word
                    $mime ='doc'; break; 
               case '.docx':   // Microsoft Word (OpenXML)
                    $mime ='docx'; break; 
               case '.eot':    // MS Embedded OpenType fonts
                    $mime ='base'; break; 
               case '.epub':   // Electronic publication (EPUB)
                    $mime ='base'; break; 
               case '.gif':    // Graphics Interchange Format (GIF)
                    $mime ='image'; break; 
               case '.htm':    // HyperText Markup Language (HTML)
                    $mime ='html'; break; 
               case '.html':   // HyperText Markup Language (HTML)
                    $mime ='html'; break; 
               case '.ico':    // Icon format
                    $mime ='image'; break; 
               case '.ics':    // iCalendar format
                    $mime ='base'; break; 
               case '.jar':    // Java Archive (JAR)
                    $mime ='base'; break; 
               case '.jpeg':   // JPEG images
                    $mime ='image'; break;
               case '.jpg':    // JPEG images
                    $mime ='image'; break; 
               case '.js':     // JavaScript (IANA Specification) (RFC 4329 Section 8.2)
                    $mime ='base'; break; 
               case '.json':   // JSON format
                    $mime ='json'; break; 
               case '.mid':    // Musical Instrument Digital Interface (MIDI)
                    $mime ='audio'; break; 
               case '.midi':   // Musical Instrument Digital Interface (MIDI)
                    $mime ='audio'; break; 
               case '.mpeg':   // MPEG Video
                    $mime ='video'; break; 
               case '.mpkg':   // Apple Installer Package
                    $mime ='base'; break; 
               case '.odp':    // OpenDocument presentation document
                    $mime ='odp'; break; 
               case '.ods':    // OpenDocument spreadsheet document
                    $mime ='ods'; break; 
               case '.odt':    // OpenDocument text document
                    $mime ='odt'; break; 
               case '.oga':    // OGG audio
                    $mime ='audio'; break; 
               case '.ogv':    // OGG video
                    $mime ='video'; break; 
               case '.ogx':    // OGG
                    $mime ='base'; break; 
               case '.otf':    // OpenType font
                    $mime ='base'; break; 
               case '.png':    // Portable Network Graphics
                    $mime ='image'; break; 
               case '.pdf':    // Adobe Portable Document Format (PDF)
                    $mime ='pdf'; break; 
               case '.ppt':    // Microsoft PowerPoint
                    $mime ='ppt'; break; 
               case '.pptx':   // Microsoft PowerPoint (OpenXML)
                    $mime ='pptx'; break; 
               case '.rar':    // RAR archive
                    $mime ='base'; break; 
               case '.rtf':    // Rich Text Format (RTF)
                    $mime ='rtf'; break; 
               case '.sh':     // Bourne shell script
                    $mime ='base'; break; 
               case '.svg':    // Scalable Vector Graphics (SVG)
                    $mime ='image'; break; 
               case '.swf':    // Small web format (SWF) or Adobe Flash document
                    $mime ='base'; break; 
               case '.tar':    // Tape Archive (TAR)
                    $mime ='base'; break; 
               case '.tif':    // Tagged Image File Format (TIFF)
                    $mime ='image'; break; 
               case '.tiff':   // Tagged Image File Format (TIFF)
                    $mime ='image'; break; 
               case '.ts':     // Typescript file
                    $mime ='base'; break; 
               case '.ttf':    // TrueType Font
                    $mime ='base'; break; 
               case '.txt':    // Text, (generally ASCII or ISO 8859-n)
                    $mime ='txt'; break; 
               case '.vsd':    // Microsoft Visio
                    $mime ='vsd'; break; 
               case '.wav':    // Waveform Audio Format
                    $mime ='audio'; break; 
               case '.weba':   // WEBM audio
                    $mime ='audio'; break; 
               case '.webm':   // WEBM video
                    $mime ='video'; break; 
               case '.webp':   // WEBP image
                    $mime ='image'; break; 
               case '.woff':   // Web Open Font Format (WOFF)
                    $mime ='base'; break; 
               case '.woff2':  // Web Open Font Format (WOFF)
                    $mime ='base'; break; 
               case '.xhtml':  // XHTML
                    $mime ='html'; break; 
               case '.xls':    // Microsoft Excel
                    $mime ='xls'; break; 
               case '.xlsx':   // Microsoft Excel (OpenXML)
                    $mime ='xlsx'; break; 
               case '.xml':    // XML
                    $mime ='xml'; break; 
               case '.xul':    // XUL
                    $mime ='base'; break; 
               case '.zip':    // ZIP archive
                    $mime ='base'; break; 
               case '.3gp':    // 3GPP audio/video container
                    $mime ='video'; break; 
               case '.3g2':    // 3GPP2 audio/video container 
                    $mime ='video'; break; 
               case '.7z':     // 7-zip archive
                    $mime ='base'; break; 
               default:        // general purpose MIME-type
                    $mime = 'base' ; 
          }

          return $mime ;
     }

}

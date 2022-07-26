<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Lib;

use CURLFile;
use SolrQuery;
use SolrDisMaxQuery;
use SolrUtils;
use SolrClient;
use SolrClientException;
use DataSearchEngine\Entity\DocumentFile;
use DataSearchEngine\Entity\CitizenDocument;

/**
 * SolrConsumer class to interact with Solr server.
 *
 * @package DataSearchEngine\Lib
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class SolrConsumer {

    /** Solr API method */
    const INDEX_FILE = '/update/extract';

    /** Solr API url for cURL */
	private $api;

	public function __construct() {
		$this->api = 'http://'.SOLR_SERVER.':'.SOLR_PORT.'/solr/'.SOLR_CORE;
    }
    
    /**
	 * Index a document in Solr with custom field values. 
	 * Complex document must be indexed through cURL request, this functionnality is not implemented yet in PHP Solr client.
	 *
	 * @param CitizenDocument $document Document to index with its metadata
	 * @param DocumentFile $file File to index with its open data URL
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return boolean
	 * @throws Exception If curl call generate an error
	 * @access public
	 */
	public function indexDocumentFile(CitizenDocument $document, DocumentFile $file) {
		$cFile = new CURLFile($file->getPath(), $file->getFormat(), $file->getName());

		// Basic configuration for writing in Solr index server
        $postConfiguration = array(
            'commitWithin'  => '1000',
            'overwrite'     => 'true',
            'wt'            => 'json',
            'commit'        => 'true'
        );
        
        $ch = curl_init();
        $options = array(
			CURLOPT_URL             => $this->api.self::INDEX_FILE,
			CURLOPT_ENCODING		=> '',
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
			CURLOPT_HEADER          => 0,
			CURLINFO_HEADER_OUT		=> true,
            CURLOPT_POST            => 1,
            CURLOPT_HTTPHEADER      => array('Content-Type: multipart/form-data', 'Authorization: Basic '.base64_encode(SOLR_USER.':'.SOLR_PASSWORD)),
            CURLOPT_POSTFIELDS      => array_merge($postConfiguration, $this->setMetadataArray($document, $file), array('myFile' => $cFile))
		);
        curl_setopt_array($ch, $options);
		curl_exec($ch);
		$result = false;
        if (curl_errno($ch) == 0) {
			$status = curl_getinfo($ch)['http_code'];
            if ($status == 200) {
                $result = true;
            } else if ($status == 401) {
				throw new \Exception('Vous n\'êtes pas autorisé à accéder au serveur d\'indexation, veuillez vérifier vos identifiants de connexion');
			}
        } else {
			$errmsg = curl_error($ch);
		}
		curl_close($ch);

		// Only if curl generate an error, throw exception.
		if (!$result && isset($errmsg)) {
			throw new \Exception($errmsg);
		}
		
		return $result;
	}

	/**
	 * Execute an admin search in Solr server
	 *
	 * @param string $filename Document filename
     * @param string $date Document date
     * @param string $siren Entity SIREN to filter results for entity only
	 * @param int $offset Start offset for search
	 * @param int $limit Limit for results display
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return SolrObject
	 * @throws \Exception If an error occured during request
	 * @access public
	 */
    public function adminSearch($filename, $date, $siren, $offset, $limit) {
        try {
            $query = new SolrQuery('*:*');
            $query->setTimeAllowed(1500);
            $query->addSortField('date', 1);

            if (!empty($siren)) {
                $query->addFilterQuery('siren:'.$siren);
            }
            if (!empty($filename)) {
                $query->addFilterQuery('filepath:"'.$filename.'"');
            }
            if (!empty($date)) {
                $query->addFilterQuery('date:'.$date.'T00\:00\:00Z');
            }
            $query->setStart($offset);
            $query->setRows($limit);

            $query->addField('siren');                  // Entity siren
            $query->addField('date');                   // Document date
            $query->addField('filepath');               // File OpenData URL
            $query->addField('content_type');           // Type of document file
            $query->addField('stream_content_type');    // Type of document file
            $query->addField('id');                     // Solr document identifier
            $query->addField('stream_name');            // Filename
    
            return $this->getSolrClient()->query($query)->getResponse();

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), '403')) {
                throw new \Exception('Vous n\'êtes pas authorisé à accèder à cette ressource', 403);
            } else {
                throw new \Exception($e->getMessage(), 500);
            }
        }
    }

	/**
	 * Execute a simple search in Solr server
	 *
	 * @param string $keywords Search keywords
     * @param string $siren Entity SIREN to filter results for entity only
     * @param string $type type of document
	 * @param int $offset Start offset for search
	 * @param int $limit Limit for results display
	 * @param boolean $qf (optional) Flag to activate queryField search for numerized documents (false by default)
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return SolrObject
	 * @throws \Exception If an error occured during request
	 * @access public
	 */
    public function simpleSearch($keywords, $siren, $type, $offset, $limit, $qf = false) {
        try {
            $query = $this->initializeQuery($siren, $offset, $limit, $keywords, $qf);

            if (!empty($type)) {
                $query->addFilterQuery('documenttype:'.SolrUtils::queryPhrase($type));
            }
    
            return $this->getSolrClient()->query($query)->getResponse();

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), '403')) {
                throw new \Exception('Vous n\'êtes pas authorisé à accèder à cette ressource', 403);
            } else {
                throw new \Exception($e->getMessage(), 500);
            }
        }
	}
	
	 /**
	 * Execute an advanced search in Solr server
	 *
	 * @param string $keywords Search keywords
     * @param string $type Document type
     * @param string $siren Entity SIREN to filter results for entity only
	 * @param string $startDate Start date from search form
	 * @param string $endDate End date from search form
	 * @param int $offset Start offset for search
	 * @param int $limit Limit for results display
	 * @param boolean $qf (optional) Flag to activate queryField search for numerized documents (false by default)
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return SolrObject
	 * @throws \Exception If an error occured during request
	 * @access public
	 */
    public function advancedSearch($keywords, $type, $siren, $startDate, $endDate, $offset, $limit, $qf = false) {
        try {
            if (!empty($keywords)) {
                $searchWords = $keywords;
            }
            $query = $this->initializeQuery($siren, $offset, $limit, $keywords, $qf);
            if (!empty($type)) {
                $query->addFilterQuery('documenttype:'.SolrUtils::queryPhrase($type));
            }
            if (!empty($startDate) || !empty($endDate)) {
                $query->addFilterQuery('date:'.$this->setDateFilter($startDate, $endDate));
            }
    
            return $this->getSolrClient()->query($query)->getResponse();

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), '403')) {
                throw new \Exception('Vous n\'êtes pas authorisé à accèder à cette ressource', 403);
            } else {
                throw new \Exception($e->getMessage(), 500);
            }
        }
    }

    /**
	 * Check if a document has already been indexed in Solr with its file hash.
	 *
	 * @param string $siren Entity Siren as index filter
	 * @param string $hash File hash
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return boolean
	 * @access public
	 */
	public function documentIsAlreadyIndexed(string $siren, string $hash) {
		$response = $this->searchDocument('', array('siren' => $siren, 'hash' => $hash));

		if (isset($response) && $response['response']['numFound'] > 0) {
			return true;
		} else {
			return false;
		}
    }
    
    /**
	 * Search documents in Solr server from search criteria.
	 *
	 * @param string $keywords Optional search keywords
	 * @param array $criteria Optional search criterias field => value
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return SolrQueryResponse->SolrObject
	 */
	private function searchDocument(string $keywords, array $criterias) {
		$query = new SolrQuery();

		// Main keywords
		if (is_null($keywords) || empty($keywords)) {
			$keywords = '*:*';
		}
		$query->setQuery($keywords);
		
		// Criteria by filter field
		if (sizeof($criterias) > 1) {
			foreach($criterias as $field => $value){
				$query->addFilterQuery($field.':'.$value);
			}
		}

		return $this->getSolrClient()->query($query)->getResponse();
	}

    /**
	 * Delete a document index in Solr by its solr document id.
	 *
	 * @param string $solrDocumentId Solr document id
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return boolean
	 * @access public
	 */
	public function deleteDocumentIndexById(string $solrDocumentId) {
		$deleteResponse = $this->getSolrClient()->deleteById($solrDocumentId);
		$commitResponse = $this->getSolrClient()->commit();
		
		// Check delete and commit request
		if ($deleteResponse->getHttpStatus() == 200 
			&& $commitResponse->getHttpStatus() == 200) {
			return true;
		} else {
			return false;
		}
	}

    /**
	 * Construct a date range filter for query.
	 *
	 * @param string $startDate Start date from search form
	 * @param string $endDate End date from search form
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return string
	 * @access protected
	 */
    private function setDateFilter($startDate, $endDate) {
        $filter = '[';
        if (empty($startDate)) {
            $filter .= '*';
        } else {
            $filter .= $startDate.'T00:00:00Z';
        }
        $filter .= ' TO ';
        if (empty($endDate)) {
            $filter .= '*';
        } else {
            $filter .= $endDate.'T00:00:00Z';
        }
        $filter .= ']';

        return $filter;
    }

    /**
     * Set a metadata array with all document informations for Solr indexation.
	 * 
     *
	 * @param CitizenDocument $document Document to index with its metadata
	 * @param DocumentFile $file File to index with its open data URL
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return array
     */
	private function setMetadataArray(CitizenDocument $document, DocumentFile $file) {
		$fieldValues = array(
			'literal.hash'					=> $file->getHash(),
			'literal.filepath'      		=> $file->getOpenDataUrl(),
			'literal.description'      		=> $document->getDescription(),
			'literal.date'					=> $date = $document->getDate()->format('Y-m-d').'T00:00:00Z',
			'literal.origin'				=> $document->getOrigin(),
			'literal.entity'        		=> $document->getCollectivite()->getName(),
			'literal.siren'         		=> $document->getCollectivite()->getSiren(),
			'literal.nic'         			=> $document->getCollectivite()->getNic(),
            'literal.adresse1'      		=> $document->getCollectivite()->getAdresse()->getNumero_voie()
            .' '.$document->getCollectivite()->getAdresse()->getType_voie().' '.$document->getCollectivite()->getAdresse()->getLibelle_voie(),
			'literal.adresse2'      		=> $document->getCollectivite()->getAdresse()->getComplement(),
			'literal.ville'         		=> $document->getCollectivite()->getAdresse()->getCommune(),
			'literal.codepostal'    		=> $document->getCollectivite()->getAdresse()->getCode_postal(),
			'literal.boitepostale'    		=> $document->getCollectivite()->getAdresse()->getBoite_postale(),
			'literal.cedex'    				=> $document->getCollectivite()->getAdresse()->getCedex()
		);

		// These following values can be NULL, not necessary to save them as empty
		if (!empty($document->getIdentifier())) {
			$fieldValues['literal.documentIdentifier'] = $document->getIdentifier();
		}
		if (!empty($document->getType())) {
			$fieldValues['literal.documentType'] = $document->getType();
		}
		if (!empty($document->getClassification())) {
			$fieldValues['literal.classification'] = $document->getClassification();
		}
		if (!empty($file->getTypology())) {
			$fieldValues['literal.typology'] = $file->getTypology();
		}

		return $fieldValues;
	}
    
    /**
	 * Initialize a Solr Dismax query with field results and some technicals parameters.
	 *
     * @param string $siren Entity SIREN to filter results for entity only
	 * @param int $offset Start offset for search
	 * @param int $limit Limit for results display
     * @param string $searchWords Keywords for search, *:* by default
     * @param boolean $qf (optional) Flag to activate queryField search for numerized documents (false by default)
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return SolrQuery
	 * @access protected
	 */
    protected function initializeQuery($siren, $offset, $limit, $keywords, $qf = false) {
        $query = new SolrDisMaxQuery();
        // Search configuration
        $query->useDisMaxQueryParser();
        $query->setQueryAlt('*:*');
        if (!empty($keywords)) {
            if ($qf) {
                $query->setQuery(SolrUtils::queryPhrase($keywords));
            } else {
                $query->setQuery(SolrUtils::escapeQueryChars($keywords));
            }
        }
        if ($qf) {
            $query->addQueryField('entity OR adresse1 OR codepostal OR ville OR description OR classification');
        }
        $query->setMinimumMatch('95%');
        $query->setTimeAllowed(1500);
        $query->setMltMinWordLength(3);
        $query->addSortField('date', 1);
        $query->addSortField('publication_id', 0);


        if (!empty($siren)) {
            $query->addFilterQuery('siren:'.$siren);
        }
        $query->addFilterQuery('est_publie:true');

        $query->setStart($offset);
        $query->setRows($limit);

        $this->setFieldsForResponse($query);

        return $query;
    }

    /**
	 * Set fields for Solr response.
	 *
     * @param SolrDisMaxQuery $query Query to be configured
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @access protected
	 */
    protected function setFieldsForResponse(SolrDisMaxQuery &$query) {
        $query->addField('entity');                 // Entity name
        $query->addField('siren');                  // Entity siren
        $query->addField('nic');                    // Entity nic : SIRET = siren+nic
        $query->addField('date');                   // Document date
        $query->addField('filepath');               // File OpenData URL
        $query->addField('documenttype');           // Document type
        $query->addField('classification');         // Classification
        $query->addField('typology');               // Typology
        $query->addField('date_de_publication');    // Date de publication
        $query->addField('content_type');           // Type of document file
        $query->addField('stream_content_type');    // Type of document file
        $query->addField('id');                     // Solr document identifier
        $query->addField('documentidentifier');     // Pastell document identifier
        $query->addField('description');            // Document short description
        $query->addField('stream_name');            // Filename
        $query->addField('blockchain_enable');      // Boolean is this publication in the blockchain
        $query->addField('blockchain_url');         // Url to the transaction
    }

	/**
	 * Get solr client
	 *
	 * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
	 * @return SolrClient
	 * @access protected
	 */
	protected function getSolrClient() {
		$config = array (
			'hostname' => SOLR_SERVER,
			'login'    => SOLR_USER,
			'password' => SOLR_PASSWORD,
			'port'     => SOLR_PORT,
			'timeout'  => 10,
			'path'     => '/solr/'.SOLR_CORE
		);

		$client = new SolrClient($config);
		$client->setResponseWriter('json');

		return $client;
	}
}

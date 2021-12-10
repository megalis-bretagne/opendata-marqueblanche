<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Lib;

use DataSearchEngine\Entity\Collectivite;
use DataSearchEngine\Entity\Adresse;

/**
 * SireneConsumer class to consume Insee Sirene API.
 *
 * @package DataSearchEngine\Lib
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class SireneConsumer {

    /**
     * Sirene V3 API key string to get token bearer
     */
    private $key;

    /**
     * Sirene V3 API secret string to get token bearer
     */
    private $secret;

    /**
     * Sirene V3 API token bearer
     */
    private $token;

    /**
     * Insee Sirene V3 API URL to get token bearer
     */
    private $inseeApi = 'https://api.insee.fr';
    const TOKEN = '/token';

    /** Insee Sirene API */
    private $sireneApi = 'https://api.insee.fr/entreprises/sirene/V3';
    const SIRENE_STATUS = '/informations';
    const SIRENE_INFORMATIONS_BY_SIREN = '/siret';

    /**
     * Constructor, get authorization token at initialization
     */
    public function __construct() {
        // At initialization, get or retrive the authorization bearer.
        $this->key      = SIRENE_KEY;
        $this->secret   = SIRENE_SECRET;
        $this->token    = $this->getAuthorizationToken();
    }

    /**
     * Get organization informations for a specific Siren. 
     * This function returns the most recent informations if the organization has an history
     *
     * @param string $siren Organization SIREN
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return object
     * @throws \Exception If curl call generate an error
     * @access public 
     */
    public function getOrganizationInformations($siren) {
        try {
            $params = 'q=siren:'.$siren;
            $response = $this->apiCurlCalling($this->sireneApi.self::SIRENE_INFORMATIONS_BY_SIREN, $this->token, $params);

            $organization = null;
            if ($response != null && isset($response->header) && $response->header->statut == 200) {
                // If organization has no history, get directly the element from response
                if (sizeof($response->etablissements) == 1) {
                    $organization = $response->etablissements[0];
                } 
                // Else get the most recent...
                else {
                    foreach ($response->etablissements as $organizationTmp) {
                        if ($organization == null) {
                            $organization = $organizationTmp;
                        } else if (strtotime($organizationTmp->dateCreationEtablissement) > strtotime($organization->dateCreationEtablissement)) {
                            $organization = $organizationTmp;
                        }
                    }
                }
                
                return $this->extractOrganizationInformations($organization);

            } else if ($response != null && isset($response->fault) && $response->fault->code == 900804) {
                // Sirene API calls limit exceeded, must wait 60 sec
                sleep(60);
                $this->getOrganizationInformations($siren);
            }
        } catch (\Exception $e) {
            throw new \Exception('Impossible de récupérer les informations de l\'entité pour le SIREN '.$siren, 500);
        }
    }
    
    /**
     * Get authorization bearer from consumer key and secret.
     *
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return string
     * @access private 
     */
    private function getAuthorizationToken() {
        try {
            $response = $this->apiCurlCalling($this->inseeApi.self::TOKEN, null, 'grant_type=client_credentials&validity_period=86400');
            return $response->access_token;
        } catch (\Exception $e) {
            throw new \Exception('Impossible de récupérer le jeton d\'authorisation Sirene', 500);
        }
    }

    /**
     * Execute a cURL call and return JSON response as object.
     *
     * @param string $url Insee API service URL
     * @param string $token Authorization bearer get from Insee account
     * @param string $params URL formed query params
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return object
     * @throws \Exception If curl call generate an error
     * @access private
     */
    private function apiCurlCalling($url, $token = null, $params = null) {
        $errmsg = null;
        $ch = curl_init();
        $options = array(
            CURLOPT_URL             => $url,
            CURLOPT_HEADER          => 0,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER  => true
        );

        if ($token != null) {
            $options[CURLOPT_HTTPHEADER] = array(
                'Accept: application/json', 
                'Authorization: Bearer '.$token
            );
        } else {
            // If token is NULL, basic authorization with consumer key and secret to get token.
            $options[CURLOPT_HTTPHEADER] = array(
                'Accept: application/json', 
                'Authorization: Basic '.base64_encode($this->key.':'.$this->secret)
            );
        }

        if ($params != null) {
            $options[CURLOPT_POSTFIELDS] = $params;
        }

        curl_setopt_array($ch, $options);
        $response = json_decode(curl_exec($ch));
        if (curl_errno($ch) != 0) {
            $errmsg = curl_error($ch);
        }
        curl_close($ch);

        if ($errmsg != null) {
            throw new \Exception($errmsg, 500);
        } else if ($response == null) {
            throw new \Exception('L\'appel du service Sirene V3 n\'a généré aucune réponse', 204);
        }

        return $response;
    }

    /**
     * Extract organization useful informations from JSON response
     *
     * @param stdClass $establishment Establishment from JSON response
     * @author Xavier MADIOT <x.madiot@girondenumerique.fr>
     * @return Collectivite
     * @access private
     */
    private function extractOrganizationInformations(\stdClass $establishment) {
        $address = new Adresse();
        if (!empty($establishment->adresseEtablissement->numeroVoieEtablissement)) {
            $address->setNumero_voie($establishment->adresseEtablissement->numeroVoieEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->typeVoieEtablissement)) {
            $address->setType_voie($establishment->adresseEtablissement->typeVoieEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->libelleVoieEtablissement)) {
            $address->setLibelle_voie($establishment->adresseEtablissement->libelleVoieEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->complementAdresseEtablissement)) {
            $address->setComplement($establishment->adresseEtablissement->complementAdresseEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->distributionSpecialeEtablissement)) {
            $address->setBoite_postale($establishment->adresseEtablissement->distributionSpecialeEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->codePostalEtablissement)) {
            $address->setCode_postal($establishment->adresseEtablissement->codePostalEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->libelleCommuneEtablissement)) {
            $address->setCommune($establishment->adresseEtablissement->libelleCommuneEtablissement);
        }
        if (!empty($establishment->adresseEtablissement->codeCedexEtablissement)) {
            $address->setCedex($establishment->adresseEtablissement->codeCedexEtablissement);
        }

        $collectivite = new Collectivite();
        $collectivite->setName($establishment->uniteLegale->denominationUniteLegale);
        $collectivite->setSiren($establishment->siren);
        $collectivite->setNic($establishment->nic);
        $collectivite->setAdresse($address);
        $collectivite->setApe_code($establishment->uniteLegale->activitePrincipaleUniteLegale);
        
        return $collectivite;
    }
}
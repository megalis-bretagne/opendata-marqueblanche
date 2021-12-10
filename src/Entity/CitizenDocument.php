<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Entity;

use DateTime;
use DataSearchEngine\Entity\Collectivite;
use DataSearchEngine\Entity\DocumentFile;

/**
 * Class CitizenDocument to describe a document with its metadata and its files.
 *
 * @package DataSearchEngine\Entity
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class CitizenDocument {

    /**
	* Type de document
	* @var string
	*/
    private $type;

    /**
	* Identifiant
	* @var string
	*/
    private $identifier;

    /**
	* Description
	* @var string
	*/
    private $description;

    /**
	* Répertoire de destination
	* @var string
	*/
    private $destinationDirectory;

    /**
	* Date du document
	* @var string
	*/
    private $date;

    /**
	* Classification du document
	* @var string
	*/
    private $classification;

    /**
	* Origine du document
	* @var string
	*/
    private $origin;

    /**
	* Collectivité concernée par le document
	* @var Collectivite
	*/
    private $collectivite;

    private $files = array();

    public function __construct(string $description, string $destinationDirectory, array $files) {
        $this->description          = $description;
        $this->destinationDirectory = $destinationDirectory;
        $this->date                 = new DateTime('NOW');
        $this->files                = $files;
    }

    /**
     * Get the value of type
     */ 
    public function getType() {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType(string $type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of identifier
     */ 
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Set the value of identifier
     *
     * @return  self
     */ 
    public function setIdentifier(string $identifier) {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription(string $description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of destinationDirectory
     */ 
    public function getDestinationDirectory() {
        return $this->destinationDirectory;
    }

    /**
     * Set the value of destinationDirectory
     *
     * @return  self
     */ 
    public function setDestinationDirectory(string $destinationDirectory) {
        $this->destinationDirectory = $destinationDirectory;
        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate() {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate(DateTime $date) {
        $this->date = $date;
        return $this;
    }

    /**
     * Get the value of classification
     */ 
    public function getClassification() {
        return $this->classification;
    }

    /**
     * Set the value of classification
     *
     * @return  self
     */ 
    public function setClassification(string $classification) {
        $this->classification = $classification;
        return $this;
    }

    /**
     * Get the value of origin
     */ 
    public function getOrigin() {
        return $this->origin;
    }

    /**
     * Set the value of origin
     * @return  self
     */ 
    public function setOrigin(string $origin) {
        $this->origin = $origin;
        return $this;
    }

    /**
     * Get the value of collectivite
     */ 
    public function getCollectivite() {
        return $this->collectivite;
    }

    /**
     * Set the value of collectivite
     *
     * @return  self
     */ 
    public function setCollectivite(Collectivite $collectivite) {
        $this->collectivite = $collectivite;
        return $this;
    }

    /**
     * Get the value of files
     */ 
    public function getFiles() {
        return $this->files;
    }

    /**
     * Set the value of files
     *
     * @return  self
     */ 
    public function setFiles(array $files) {
        $this->files = $files;
        return $this;
    }
}
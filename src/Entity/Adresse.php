<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Entity;

/**
 * Class Address
 *
 * @package DataSearchEngine\Entity
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class Adresse {

	/**
	* Numéro de voie
	* @var string
	*/
	private $numero_voie;

	/**
	* Type de voie
	* @var string
	*/
	private $type_voie;

	/**
	* Libellé de la voie
	* @var string
	*/
	private $libelle_voie;

	/**
	* Complément d'adresse
	* @var string
	*/
	private $complement;

	/**
	* Boîte postale
	* @var string
	*/
	private $boite_postale;

	/**
	* Code postal
	* @var string
	*/
	private $code_postal;

	/**
	* Libellé de la commune
	* @var string
	*/
	private $commune;

	/**
	* CEDEX
	* @var string
	*/
	private $cedex;

    /**
	 * Default constructor
	 */
	function __construct() {
		
	}

	/**
	 * Get the value of numero_voie
	 *
	 * @return  string
	 */ 
	public function getNumero_voie() {
		return $this->numero_voie;
	}

	/**
	 * Set the value of numero_voie
	 *
	 * @param  string  $numero_voie
	 * @return  self
	 */ 
	public function setNumero_voie(string $numero_voie) {
		$this->numero_voie = $numero_voie;
		return $this;
	}

	/**
	 * Get the value of type_voie
	 *
	 * @return  string
	 */ 
	public function getType_voie() {
		return $this->type_voie;
	}

	/**
	 * Set the value of type_voie
	 *
	 * @param  string  $type_voie
	 * @return  self
	 */ 
	public function setType_voie(string $type_voie) {
		$this->type_voie = $type_voie;
		return $this;
	}

	/**
	 * Get the value of libelle_voie
	 *
	 * @return  string
	 */ 
	public function getLibelle_voie() {
		return $this->libelle_voie;
	}

	/**
	 * Set the value of libelle_voie
	 *
	 * @param  string  $libelle_voie
	 * @return  self
	 */ 
	public function setLibelle_voie(string $libelle_voie) {
		$this->libelle_voie = $libelle_voie;
		return $this;
	}

	/**
	 * Get the value of complement
	 *
	 * @return  string
	 */ 
	public function getComplement() {
		return $this->complement;
	}

	/**
	 * Set the value of complement
	 *
	 * @param  string  $complement
	 * @return  self
	 */ 
	public function setComplement(string $complement) {
		$this->complement = $complement;
		return $this;
	}

	/**
	 * Get the value of boite_postale
	 *
	 * @return  string
	 */ 
	public function getBoite_postale() {
		return $this->boite_postale;
	}

	/**
	 * Set the value of boite_postale
	 *
	 * @param  string  $boite_postale
	 * @return  self
	 */ 
	public function setBoite_postale(string $boite_postale) {
		$this->boite_postale = $boite_postale;
		return $this;
	}

	/**
	 * Get the value of code_postal
	 *
	 * @return  string
	 */ 
	public function getCode_postal() {
		return $this->code_postal;
	}

	/**
	 * Set the value of code_postal
	 *
	 * @param  string  $code_postal
	 * @return  self
	 */ 
	public function setCode_postal(string $code_postal) {
		$this->code_postal = $code_postal;
		return $this;
	}

	/**
	 * Get the value of commune
	 *
	 * @return  string
	 */ 
	public function getCommune() {
		return $this->commune;
	}

	/**
	 * Set the value of commune
	 *
	 * @param  string  $commune
	 * @return  self
	 */ 
	public function setCommune(string $commune) {
		$this->commune = $commune;
		return $this;
	}

	/**
	 * Get the value of cedex
	 *
	 * @return  string
	 */ 
	public function getCedex() {
		return $this->cedex;
	}

	/**
	 * Set the value of cedex
	 *
	 * @param  string  $cedex
	 * @return  self
	 */ 
	public function setCedex(string $cedex) {
		$this->cedex = $cedex;
		return $this;
	}

	/**
	 * To string
	 */
	public function __toString() {
		try {
			return $this->siren.$this->nic.' - '.$this->name;
        } catch (\Exception $exception) {
            return '';
        }
	}
}
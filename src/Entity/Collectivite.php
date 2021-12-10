<?php
/**
 * @license Apache 2.0
 */

namespace DataSearchEngine\Entity;

use DataSearchEngine\Entity\Adresse;

/**
 * Class Collectivite
 *
 * @package DataSearchEngine\Entity
 * @author  Xavier MADIOT <x.madiot@girondenumerique.fr>
 */
class Collectivite {

	/**
	* Numéro SIREN de la collectivité
	* @var string
	*/
	private $siren;

	/**
	* Numéro NIC de la collectivité
	* @var string
	*/
	private $nic;

	/**
	* Nom de la collectivité
	* @var string
	*/
	private $name;

	/**
	* Code APE
	* @var string
	*/
	private $ape_code;

	/**
	* Adresse de la collectivité
	* @var Adresse
	*/
	private $adresse;

    /**
	 * Default constructor
	 */
	function __construct() {
		
	}

	/**
	 * Get the value of siren
	 *
	 * @return  string
	 */ 
	public function getSiren() {
		return $this->siren;
	}

	/**
	 * Set the value of siren
	 *
	 * @param  string  $siren
	 * @return  self
	 */ 
	public function setSiren(string $siren) {
		$this->siren = $siren;
		return $this;
	}

	/**
	 * Get the value of nic
	 *
	 * @return  string
	 */ 
	public function getNic() {
		return $this->nic;
	}

	/**
	 * Set the value of nic
	 *
	 * @param  string  $nic
	 * @return  self
	 */ 
	public function setNic(string $nic) {
		$this->nic = $nic;
		return $this;
	}

	/**
	 * Get the value of name
	 *
	 * @return  string
	 */ 
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @param  string  $name
	 * @return  self
	 */ 
	public function setName(string $name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Get the value of ape_code
	 *
	 * @return  string
	 */ 
	public function getApe_code() {
		return $this->ape_code;
	}

	/**
	 * Set the value of ape_code
	 *
	 * @param  string  $ape_code
	 * @return  self
	 */ 
	public function setApe_code(string $ape_code) {
		$this->ape_code = $ape_code;
		return $this;
	}

	/**
	 * Get the value of adresse
	 *
	 * @return  Adresse
	 */ 
	public function getAdresse() {
		return $this->adresse;
	}

	/**
	 * Set the value of adresse
	 *
	 * @param  Adresse  $adresse
	 * @return  self
	 */ 
	public function setAdresse(Adresse $adresse) {
		$this->adresse = $adresse;
		return $this;
	}

	/**
	 * To string
	 */
	public function __toString() {
		try {
			return $this->siren.' - '.$this->nic.' - '.$this->adresse;
        } catch (\Exception $exception) {
            return '';
        }
	}
}
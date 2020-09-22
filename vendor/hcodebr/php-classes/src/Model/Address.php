<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Address extends Model {

	const SESSION_ERROR = "AddressError";

	protected $fields = [
		"desaddress", 
		"descity", 
		"descomplement",
		"desdistrict",
		"descountry", 
		"desstate", 
		"dtregister", 
		"idaddress", 
		"idperson", 
		"deszipcode"
	];

	public static function getCEP($nrcep)
	{

		$nrcep = str_replace("-", "", $nrcep);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $json = curl_exec($ch);

//var_dump($json); exit;

		$data = json_decode($json, true);

		curl_close($ch);

		return $data;

	}

	public function loadFromCEP($nrcep)
	{

		$data = Address::getCEP($nrcep);

		if (isset($data['logradouro']) && $data['logradouro'] != "") 
		{

			$this->setdesaddress($data['logradouro']);
			$this->setdescomplement($data['complemento']);
			$this->setdesdistrict($data['bairro']);
			$this->setdescity($data['localidade']);
			$this->setdesstate($data['uf']);
			$this->setdescountry('Brasil');
			$this->setdeszipcode($nrcep);

		}

	}

	public function save()
	{

		$sql = new Sql();

        $bind = [
			':idaddress'=>$this->getidaddress(),
			':idperson'=>$this->getidperson(),
			':desaddress'=>utf8_encode($this->getdesaddress()),
			':desnumber'=>$this->getdesnumber(),
			':descomplement'=>utf8_encode($this->getdescomplement()),
			':descity'=>utf8_encode($this->getdescity()),
			':desstate'=>utf8_encode($this->getdesstate()),
			':descountry'=>utf8_encode($this->getdescountry()),
			':deszipcode'=>$this->getdeszipcode(),
			':desdistrict'=>utf8_encode($this->getdesdistrict())
		];

//var_dump($bind); exit;

		$results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", $bind);

		if (count($results) > 0) 
		{

			$this->setData($results[0]);

		}

	}

	public static function setMsgError($msg)
	{

		$_SESSION[Address::SESSION_ERROR] = $msg;

	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";

		Address::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[Address::SESSION_ERROR] = NULL;

	}

}

?>
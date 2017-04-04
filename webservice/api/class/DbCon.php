<?php
include 'Country.php';
include 'City.php';


class DbCon{
    
    private $host = 'localhost';
    private $database = 'runm17_wi4_sde_dk';
    private $username = 'runm17.wi4';
    private $password = '4ykpk3p2';
    
    private $objCon;
    
    public function __construct() {
        $this->objCon = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if($this->testDbConnection()){
            $this->objCon->set_charset('utf8');
            return $this->objCon;
        }
    }
    
    public function testDbConnection(){
        if($this->objCon->connect_error){
            die('Der er ikke fobindelse til database: ' . 
                $this->objCon->connect_errno . ' ' .  
                $this->objCon->connect_error);
        } else {
            return TRUE;
        }
    }
    
	// GETTERS
    public function getAllCities(){
        $sql = 	'SELECT dynweb__cities.city_id, dynweb__cities.zip_code, dynweb__cities.city_name, dynweb__countries.country_id, dynweb__countries.country_code, dynweb__countries.country_name '
				.'FROM dynweb__cities'
				.' INNER JOIN dynweb__countries on dynweb__cities.country_id = dynweb__countries.country_id';
        $result = $this->objCon->query($sql);
		
		
        $cities = array();
        while($row = $result->fetch_object()){
            
            $country = new Country($row->country_name, $row->country_code, $row->country_id);
            $cities[] = new City($row->zip_code, $row->city_name, $country, $row->city_id);
        }
        
        return $cities; 
    }
	public function getCityByZipCode($zipcode) {		
        $sql = 	'SELECT dynweb__cities.city_id, dynweb__cities.zip_code, dynweb__cities.city_name, dynweb__countries.country_id, dynweb__countries.country_code, dynweb__countries.country_name'
				.' FROM dynweb__cities'
				.' INNER JOIN dynweb__countries on dynweb__cities.country_id = dynweb__countries.country_id'
				.' WHERE dynweb__cities.zip_code=?';
				
		if ($stmt = $this->objCon->prepare($sql)) {
			$stmt->bind_param("i", $zipcode);
			
			$stmt->execute();
			$cities = array();
			$stmt->bind_result($ci_id, $ci_zip, $ci_cn, $co_id, $co_co, $co_cn);
			while ($stmt->fetch()) {
				$country = new Country($co_cn, $co_co, $co_id);
				$cities[] = new City($ci_zip, $ci_cn, $country, $ci_id);
			}
			
		}

        return $cities; 
	}
	public function getCityByName($name) {
		$name = urldecode($name);
        $sql = 	'SELECT dynweb__cities.city_id, dynweb__cities.zip_code, dynweb__cities.city_name, dynweb__countries.country_id, dynweb__countries.country_code, dynweb__countries.country_name'
				.' FROM dynweb__cities'
				.' INNER JOIN dynweb__countries on dynweb__cities.country_id = dynweb__countries.country_id'
				." WHERE dynweb__cities.city_name LIKE CONCAT('%',?,'%') LIMIT 10";
				
		if ($stmt = $this->objCon->prepare($sql)) {
			$stmt->bind_param("s", $name);
			
			$stmt->execute();
			$cities = array();
			$stmt->bind_result($ci_id, $ci_zip, $ci_cn, $co_id, $co_co, $co_cn);
			while ($stmt->fetch()) {
				$country = new Country($co_cn, $co_co, $co_id);
				$cities[] = new City($ci_zip, $ci_cn, $country, $ci_id);
			}
		}
        return $cities; 
	}
	public function getAllCountries() {
       $sql = 	'SELECT country_id, country_code, country_name'
				.' FROM dynweb__countries';
        $result = $this->objCon->query($sql);
		
		
        $countries = array();
        while($row = $result->fetch_object()){
            
            $countries[] = new Country($row->country_name, $row->country_code, $row->country_id);
        }
        
        return $countries; 
	}
	public function getCountryByCode($code) {
		$code = urldecode($code);
	   $sql = 	'SELECT country_id, country_code, country_name'
		.' FROM dynweb__countries'
		.' WHERE country_code = ?';
				
		if ($stmt = $this->objCon->prepare($sql)) {
			$stmt->bind_param("s", $code);
			
			$stmt->execute();
			$countries = array();
			$stmt->bind_result($co_id, $co_co, $co_cn);
			if ($stmt->fetch()) {
				$countries[] = new Country($co_cn, $co_co, $co_id);
			}
			else {$countries[] = "ERROR couldn't find any country with: $code";}
		}
        return $countries;
	}
	public function getCountryByName($name) {
		$name = urldecode($name);
	   $sql = 	'SELECT country_id, country_code, country_name'
		.' FROM dynweb__countries'
		." WHERE country_name like CONCAT('%',?,'%')";
				
		if ($stmt = $this->objCon->prepare($sql)) {
			$stmt->bind_param("s", $name);
			
			$stmt->execute();
			$countries = array();
			$stmt->bind_result($co_id, $co_co, $co_cn);
			while ($stmt->fetch()) {
				$countries[] = new Country($co_cn, $co_co, $co_id);
			}
		}
		
        return $countries;
	}
	
	// CREATORS
	public function createNewCity($input) {
			
		$decoded = json_decode($input);
		$country = $this->getCountryByCode($decoded->countryCode);
		$city	 = new City($decoded->zipCode, $decoded->cityName, $country[0]);
		
		// start by getting the country ID out of the database.
		$sql = "INSERT INTO `dynweb__cities`(`zip_code`, `city_name`, `country_id`) VALUES (?,?,?)";
		if ($stmt = $this->objCon->prepare($sql)) {
			
			$stmt->bind_param("isi", $city->zipCode, $city->cityName, $city->country->countryId);
			$stmt->execute();
			
			if ($stmt->affected_rows > 0) {
				$city->cityId = $this->objCon->insert_id;
				$stmt->close();
				return (object) array("succes" => true, "city" => $city);
			} 
			else {
				$stmt->close();
				return (object) array("success" => false, "error" => "Something went wrong, city was not created.");
			}
		}
		else {
			return (object) array("success" => false, "error" => "Something went wrong in your SQL syntax");
		}
	}
	
	//Deleters
	public function deleteCityById($cityId) {
		$sql = "DELETE FROM `dynweb__cities` WHERE city_id = ?";
		if ($stmt = $this->objCon->prepare($sql)) {
			$stmt->bind_param("i", $cityId);

			$stmt->execute();
			
			if ($stmt->affected_rows > 0) {
				$stmt->close();
				return (object) array("success" => true, "city" => "The city was successfully deleted");
			}
			else {
				$stmt->close();
				return (object) array("success" => false, "msg" => $this->objCon->error);
			}
		}
		else {
			return (object) array("success" => false, "msg" => "Something went wrong in your SQL syntax");
		}
	}
	public function deleteCityByName($cityName) {
		$sql = "DELETE FROM `dynweb__cities` WHERE city_name = ?";
		if ($stmt = $this->objCon->prepare($sql)) {
			$stmt->bind_param("s", $cityName);

			$stmt->execute();
			
			if ($stmt->affected_rows > 0) {
				$stmt->close();
				return (object) array("success" => true, "city" => "The city was successfully deleted");
			}
			else {
				$stmt->close();
				return (object) array("success" => false, "msg" => $this->objCon->error);
			}
		}
		else {
			return (object) array("success" => false, "msg" => "Something went wrong in your SQL syntax");
		}
	}
}
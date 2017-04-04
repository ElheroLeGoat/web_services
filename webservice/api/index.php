<?php
error_reporting(-1);
ini_set('display_errors', 'On');

include_once 'class/DbCon.php';

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$uri = explode("/", $uri);

foreach($uri AS $key=>$value){
    unset($uri[$key]);
    
    if($value == 'api'){
        break;
    }
}

$uri = array_values($uri);
$dbCon = new DbCon();

// Run Service handler.
if ($httpMethod === "GET") {

if ($uri[0] === "") {	
	echo "<h1 style=\"font-family:sans-serif;\">City Web service</h1>"
		."<br> <p style=\"font-family:sans-serif;\">following urls can be used:</p>"
		."<ol style=\"font-family:sans-serif;\">"
		."<li>".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']."dumpcities</li>"
		."<li>".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']."byzipcode/xxxx</li>"
		."<li>".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']."byname/something</li>"
		."<li>".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']."dumpcountries</li>"
		."<li>".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']."bycode</li>"
		."<li>".$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']."bycname/something</li>"
		."<h3 style=\"font-family:sans-serif;\"> dumpcities - dumps all the cities in the database</h3>"
		."<h3 style=\"font-family:sans-serif;\">byzipcode lets you search on a zipcode by replacing \"xxxx\" with 4 digits</h3>"
		."<h3 style=\"font-family:sans-serif;\">byname lets you search on a city name by removing \"something\" .. you don't need to write the entire name. \"kø\" is enough to show københavn"
		."<h3 style=\"font-family:sans-serif;\"> dumpcountries - dumps all the countries in the database</h3>"
		."<h3 style=\"font-family:sans-serif;\"> bycode - lets you search by country code (ONLY 2 characters allowed)</h3>"
		."<h3 style=\"font-family:sans-serif;\">bycname lets you search on a country name by removing \"something\" .. you don't need to write the entire name. \"de\" is enough to show Denmark";
}
else if (strtolower($uri[0]) === "dumpcities") {
	$cities = $dbCon->getAllCities();
	header('Content-type: application/json charset=utf-8');
	echo json_encode($cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
else if (strtolower($uri[0]) === "dumpcountries") {
	$countries = $dbCon->getAllCountries();
	header('Content-type: application/json charset=utf-8');
	echo json_encode($countries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
else if (strtolower($uri[0]) === "byzipcode" && strlen($uri[1]) === 4 && is_numeric($uri[1])) {
	$cities = $dbCon->getCityByZipCode($uri[1]);
	header('Content-type: application/json charset=utf-8');
	echo json_encode($cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
else if (strtolower($uri[0]) === "bycode" && strlen($uri[1]) === 2 && ctype_alpha($uri[1])) {
	$country = $dbCon->getCountryByCode($uri[1]);
	header('Content-type: application/json charset=utf-8');
	echo json_encode($country, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
else if (strtolower($uri[0]) === "byname" && strlen($uri[1]) > 2 && strlen($uri[1]) < 75) {
	$cities = $dbCon->getCityByName($uri[1]);
	header('Content-type: application/json charset=utf-8');
	echo json_encode($cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
else if (strtolower($uri[0]) === "bycname" && strlen($uri[1]) > 1 && strlen($uri[1]) < 75) {
	$countries = $dbCon->getCountryByName($uri[1]);
	header('Content-type: application/json charset=utf-8');
	echo json_encode($countries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
else {
	echo "<h1>404 - Page not found :(</h1>";
	
}
}
else if ($httpMethod === "POST") {
	
	
			$raw_post	= file_get_contents("php://input");
			$decoded	= json_decode($raw_post);
			
		if (strtolower($uri[0]) === "createcity" && strlen($decoded->zipCode) === 4 && is_numeric($decoded->zipCode) && strlen($decoded->cityName) > 5 && strlen($decoded->cityName) < 75 && strlen($decoded->countryCode) === 2 ) {

		if ($object = $dbCon->createNewCity($raw_post)) {
			header('Content-type: application/json charset=utf-8');
			echo json_encode($object, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}
		else {
			$returnVal = (object) array("success" => false, "msg" => "Error creating the city.");
		}
    }
	
	
}
else if ($httpMethod === "DELETE") {
    if (strtolower($uri[0]) === "deletecitybyid" && is_numeric($uri[1])) {
        //GET CITY BY NAME
		if ($dbCon->deleteCityById($uri[1])) {
			
			$returnVal = (object) array("success" => true, "id" => $uri[1]);
			header('Content-type: application/json charset=utf-8');
			echo json_encode($returnVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}
		else {
			$returnVal = (object) array("success" => false, "msg" => "City could not be deleted (we suspect it doesn't exist)");
			header('Content-type: application/json charset=utf-8');
			echo json_encode($returnVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}
    }
    if (strtolower($uri[0]) === "deletecitybyname" && strlen($uri[1]) > 5 && strlen($uri[1]) < 75) {
        //GET CITY BY NAME
		if ($dbCon->deleteCityByName($uri[1])) {
			
			$returnVal = (object) array("success" => true, "name" => $uri[1]);
			header('Content-type: application/json charset=utf-8');
			echo json_encode($returnVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}
		else {
			$returnVal = (object) array("success" => false, "msg" => "City could not be deleted (we suspect it doesn't exist)");
			header('Content-type: application/json charset=utf-8');
			echo json_encode($returnVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}
    }
}
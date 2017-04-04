<?php

include_once 'class/Curl.php';
include_once 'functions/function.php';

$HTTPM  = $_SERVER["REQUEST_METHOD"];
$uri    = $_SERVER["REQUEST_URI"];



$uri_exp = explode("/", $uri);


foreach ($uri_exp AS $key=>$val) {
    unset($uri_exp[$key]);
    
    if ($val == "website") {
        break;
    }
}

$uri = array_values($uri_exp);
// RUN HANDLER
  $curl = new Curl();
if ($HTTPM === "GET") {
  
    if ($uri[0] === "") {
        // INDEX PAGE
        include_once "post.php";
    }   
    else if (strtolower($uri[0]) === "city" && strtolower ($uri[1]) === "getall") {
        // GET CITIES
        $result = $curl->get("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/dumpcities");

        $decode = json_decode($result);

        foreach($decode as $city) {
            if (is_object($city)) {
                echo $city->zipCode . " ". $city->cityName . ", " . $city->country->countryName . " (".$city->country->countryCode.")" . "<br>";
            }
        }
                
    }
    else if (strtolower($uri[0]) === "city" && strtolower($uri[1]) === "byzip" && strlen($uri[2]) === 4 && is_numeric($uri[2])) {
        // GET CITY BY ZIPCODE
        $result = $curl->get("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/byzipcode/".$uri[2]);

        $decode = json_decode($result);

        foreach($decode as $city) {
            if (is_object($city)) {
               echo $city->zipCode . " ". $city->cityName . ", " . $city->country->countryName . " (".$city->country->countryCode.")" . "<br>";
            }
        }
    }
    else if (strtolower($uri[0]) === "city" && strtolower($uri[1]) === "byname" && strlen($uri[2]) > 1 && strlen($uri[2]) < 75) {
        // GET CITY BY NAME
        $result = $curl->get("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/byname/".$uri[2]);

        $decode = json_decode($result);

        foreach($decode as $city) {
            if (is_object($city)) {
                echo $city->zipCode . " ". $city->cityName . ", " . $city->country->countryName . " (".$city->country->countryCode.")" . "<br>";
            }
        }
    }
    else if (strtolower($uri[0]) === "country" && strtolower($uri[1]) === "getall") {
        // GET COUNTRIES
        $result = $curl->get("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/dumpcountries");
         
        $decode = json_decode($result);
        
        echo "<table>"
            ."<th>Country name</th><th>Country code</th>";
        foreach($decode as $country) {
            if (is_object($country)) {
                
                echo "<tr><td>".$country->countryName . "</td><td>". $country->countryCode ."</td></tr>";
            }
        }
    }
    else if (strtolower($uri[0]) === "country" && strtolower($uri[1]) === "bycode" && strlen($uri[2]) > 1 && strlen($uri[2]) < 3) {
        // GET COUNTRY BY COUNTRY CODE
         $result = $curl->get("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/bycode/".$uri[2]);
         
        foreach($decode as $country) {
            if (is_object($country)) {
                echo $country->countryName . " (". $country->countryCode .") <br>";
            }
        }
    }
    else if (strtolower($uri[0]) === "country" && strtolower($uri[1]) === "byname" && strlen($uri[2]) > 1 && strlen($uri[2]) < 75) {
        // GET COUNTRY BY COUNTRY NAME
        $result = $curl->get("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/bycname/".$uri[2]);
        
        foreach($decode as $country) {
            if (is_object($country)) {
                echo $country->countryName . " (". $country->countryCode .") <br>";
            }
        }
    }
    else {
        header("HTTP/1.0 404 Not Fond");
    }
}
else if ($HTTPM === "POST") {
    if (strtolower($uri[0]) === "city" && strtolower($uri[1]) === "create" && validateStr($_POST['zipcode'], 1, 4, 4) && validateStr($_POST['cityname'], 2, 5, 75) && validateStr($_POST['countrycode'], 2, 2, 2) ) {
        
        $raw_post = (object) array("zipCode" => $_POST["zipcode"], "cityName" => $_POST["cityname"], "countryCode" => $_POST["countrycode"]);
        $raw_post = json_encode($raw_post);
        $result = $curl->post("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/createCity/", $raw_post);
        $obj = json_decode($result);
        header('Content-type: application/json charset=utf-8');
        echo json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
else if ($HTTPM === "DELETE") {
    if (strtolower($uri[0]) === "city" && strtolower($uri[1]) === "delete" && is_numeric($uri[2])) {
        $result = $curl->delete("http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/api/deleteCity/".$uri[2]);
        $decoded = json_decode($result);
        
        if ($decoded->success == true) {
            echo "city with ID: ".$decoded->id." has been deleted";
        }
        else {
            echo "city was not deleted". $decoded->msg;
        }
    }
}
else {
    header("HTTP/1.0 404 Not Fond");
}
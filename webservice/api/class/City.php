<?php

class City{
    public $cityId;
    public $cityName;
    public $zipCode;
    public $country;
    
    public function __construct($zipCode, $cityName, Country $country, $cityId = NULL) {
        $this->cityId = $cityId;
        $this->cityName = $cityName;
        $this->zipCode = $zipCode;
        $this->country = $country;
    }
}
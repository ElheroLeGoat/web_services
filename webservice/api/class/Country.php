<?php

class Country{
    
    public $countryId;
	public $countryCode;
    public $countryName;
    
    public function __construct($countryName, $countryCode, $countryId = NULL) {
        $this->countryName = $countryName;
		$this->countryCode = $countryCode;
        $this->countryId = $countryId;
    }
}

<?php
	class generateXML {
		
		// Database variable
		protected $db;
		
		//other
		protected $name;
		protected $child;
		
		public function __construct($mysqli) {
			
			$this->db 		= $mysqli;
			$this->child	= "";
		}
		
		public function OnlyGetCountry() {
			
			if ($stmt = $this->db->prepare("SELECT country_name FROM dynweb__countries")) {
				
				if (!$stmt->execute()) {
					echo "dafuq went wrong?";
					var_dump($this->db);
					die();
				}
				
				$stmt->bind_result($data);
				while ($stmt->fetch()) {
					$this->addToDoc($data);
				}
				$stmt->close();
			}
			$this->createDoc();
		}
		public function GetCountryAndCode() {
			if ($stmt = $this->db->prepare("SELECT country_code, country_name FROM dynweb__countries")) {
				
				$stmt->execute();
				
				$stmt->bind_result($code, $name);
				while ($stmt->fetch()) {
					$data = array($code => $name);
					$this->addToDoc($data);
				}
				$stmt->close();
			}
		$this->createDoc();
		}
		
		
		public function setFilename($name) {
			$this->name = $name;
		}
		
		protected function addToDoc($data) {
			if (is_array($data)) {
				foreach($data as $key => $value) {
					$this->child .="<country country_code=\"$key\" country_name=\"$value\" />\n";
				}
			}
			else {
				$this->child .= "<country country_name=\"$data\" /> \n";
			}
		}
		
		protected function createDoc() {
			
		$xml_start			= '<?xml version="1.0" encoding="UTF-8" ?>';
		$parent_start		= '<countries>';
		$parent_end			= '</countries>';
		
		$xml = $xml_start ."\n" . $parent_start . "\n" . $this->child . "\n" . $parent_end;
		
		$file = fopen("xml/".$this->name.".xml", "w");
		fwrite($file, $xml);
		fclose($file);
			header("location: success.php?fn=".$this->name);
		}		
	}
?>
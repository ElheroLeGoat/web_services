<?php

// Initiate the Class FeedCreater. This class is used to generate RSS feeds.

class feedCreator {
	public $ff;
	/*
	* @var:	 mysqli
	* @type: Database object
	* @desc: the object started using new mysqli()
	*/
	private $mysqli;
	
	/*
	* @var:	 url
	* @type: string
	* @desc: The url to the news page.
	*/
	private $url;
	
	/*
	* @var:	 siteName
	* @type: str
	* @desc: The name of the website
	*/
	private $siteName;

	/*
	* @var:	 siteLink
	* @type: str
	* @desc: The link to the websites front page
	*/	
	private $siteLink;
	
	/*
	* @var:	 siteDescription
	* @type: str
	* @desc: The description of the website.
	*/
	
	private $siteDescription;
	/*
	* @var:	 table
	* @type: string
	* @desc: The name of the table.
	*/
	private $table;
	
	/*
	* @var:	 data
	* @type: assoc array
	* @desc: used to store data fromt he database.
	*/
	private $data;
	
	/*
	* @var:	 xmlStr
	* @type: string
	* @desc: used to store the complete xml string.
	*/
	private $xmlStr;
	
	
	
	
	//////////////////////
	// PUBLIC FUNCTIONS //
	//////////////////////
	
	/*
	* @function: __construct
	*
	* @params
	* 	.obj $mysqli (containing the mysqli object).
	*
	*
	* @return none
	*/
	public function __construct($mysqli) {
		$this->mysqli	= $mysqli;
		$this->url		= "news.php";
		$this->data		= array();
		$this->folderCheck();
	}
	
	/**
	* @function: setUrl() (sets the url to the news page where 1 news is shown at a time)
	*
	* @params
	*	.str $url (The link to the page where a single news are shown);
	*
	* @return none
	*/
	public function setUrl($url) {
		$this->url = $url;
	}
	public function setTable($url) {
		$this->table = $table;
	}	
	public function setSiteName($sn) {
		$this->siteName = $sn;
	}
	public function setSiteLink($link) {
		$this->siteLink = $url;
	}
	public function setSteiDescription($desc) {
		$this->siteDescription = $desc;
	}
	public function setAll($all, $table, $sn, $link, $desc) {
		if (is_array($all)) {
			$this->url 				= $all["newsLink"];
			$this->table 			= $all["newsTable"];
			$this->siteName 		= $all["siteName"];
			$this->siteLink 		= $all["siteLink"];
			$this->siteDescription 	= $all["siteDesc"];
		}
		else {
			$this->url 				= $all;
			$this->table 			= $table;
			$this->siteName 		= $sn;
			$this->siteLink 		= $link;
			$this->siteDescription	= $desc;
		}	
	}
	
	/*
	* @function: getNews() (Gets the title and the id from the news section.)
	*
	* @params
	* @return none
	*/
	public function getNews() {
		// Prepared statement for optimal security
		if (!$stmt = $this->mysqli->prepare("SELECT id, title FROM ". $this->table ." ORDER BY id DESC LIMIT 10")) {
			throw new Exception("ERROR in the SQL syntax: ".$this->mysqli->error);
		}
		else {
			if (!$stmt->execute()) {
				throw new Exception("Could not get data from Database: ".$this->mysqli->error);
			}
			else {
				$stmt->bind_result($id, $news_header);
				
				while ($stmt->fetch()) {
					$this->data[$id] = $news_header;
				}
			}
			$stmt->close();
		}
		// Filter the data array and check if there's anything in it.
		$this->data = array_filter($this->data);
		if (empty($this->data)) {}
		else {
			return true;
		}
		return false;
	}
	
	public function createRSS() {
		if ($this->getNews()) {
		if ($this->generateXMLstring()) {
			$this->createDoc();
		}
		}
	}
	
	/////////////////////////
	// PROTECTED FUNCTIONS //
	/////////////////////////
	
	/*
	* @function: generateXMLstring() - Generates the string used to create the document.
	*
	* @params
	*	none
	*
	* @return boolean
	*/
	protected function generateXMLstring() {
		$xml_start			= '<?xml version="1.0" encoding="UTF-8" ?>';
		$rss_start			= '<rss version="2.0">';
		$rss_end			= '</rss>';
		$channel_start		= '<channel>';
		$channel_end		= '</channel>';
		
		$channel_info		=	'<title>'. $this->siteName. '</title>
								 <link>'. $this->siteUrl. '</link>
								 <description>'. $this->siteDescription .'</description>';
		
		
		if (is_array($this->data)) {
			$items = "";
			foreach($this->data as $key => $value) {
				$items .=
						 "<item>\n"
						."<title>\n"
						.$value."\n"
						."</title>"
						."<link>". $this->url ."?id=". $key ."</link>\n"
						."<guid>". $this->url ."?id=". $key ."</guid>\n"
						."</item>\n";
			}
			
			
			
			$this->xmlStr =
						$xml_start 		."\n". 
						$rss_start 		."\n".
						$channel_start 	."\n".
						$channel_info	."\n".
						$items			."\n".
						$channel_end	."\n".
						$rss_end;
						
			return true;
		}
		else {
			throw new Exception("Something went wrong, the data you requested didn't transform into an array");
		}
		return false;
	}
	
	/*
	* @function: createDoc() creates the file.
	*
	* @params
	*	none
	*
	* @return boolean
	*/
	protected function createDoc() {
		if (!$file = fopen("xml/feed.xml", "w")) {
			throw new Exception("Could not open/create xml/feed.xml");
			return false;
		}
		fwrite($file, $this->xmlStr);
		fclose($file);
		return true;
	}

	
	protected function folderCheck() {
		$path = "../xml/";
		if (!file_exists($path)) {
			if (!mkdir($path)) {
				throw new Exception("path: ".$path."Does not exists and the system wasn\'t able to create it.");	
				return false;
			}
		}
		return true;
	}
}
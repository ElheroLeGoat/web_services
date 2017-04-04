<?php
if (isset($_POST["title"])) {
	require("core/rssxmlClass.php");
	require("core/functions.php");
	require("core/config.php");

	
	$title 		= validateString($_POST["title"], "Overskriften", "string", 255, 10);
	$news 		= validateString($_POST["news"], "nyheden", "string", 25000, 50);
	$created 	= false;
	if ($news["success"] !== 0 && $title["success"] !== 0) {
			$date = new DateTime();
			$date = $date->format("Y-m-d H:i:s");
			
			

		$sql	= "INSERT INTO `dynweb__news`(`id`, `title`, `news`, `date`) VALUES (Null, '".$title["modified"]."','".$news["modified"]."','".$date."')";
		$error	= create_row($sql);


		if (is_numeric($error) && $error !== 0) {
			$created = true;
			try {
				$array = array("newsLink" => "http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/03_rssxml/opg_02/news.php", "newsTable" => "dynweb__news", "siteName" => "RKM - Nyheder", "siteLink" => "http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/03_rssxml/opg_02/index.php", "siteDesc" => "Mit nyheds system med RSS FEED læser.");
				$obj = new FeedCreator($mysqli);
				$obj->setAll($array);
				$obj->createRSS();
			}
			catch (Exception $e) {
				echo "problem loading FeedCreator: ".$e->getMessage();
			}
		
		
		} 
	}
	else {
		if ($title["success"] == 0) {
			foreach ($title["errors"] as $error) {
				echo $error."<br>";
			}
		}

		if ($news["success"] == 0) {
			foreach($news["errors"] as $error) {
				echo $error."<br>";
			}
		}
	}
}
?>


<html>
	<head>
		<meta charset="utf-8">
		<title>Create news</title>

		<style>
			label, input {
			display:block;
			width:250px;
			}
			textarea {
			width:250px;
			}

		</style>
	</head>

	<body>
		<?php
		if ($created == true) {
		echo '<h2><a href="index.php">Nyhed oprettet</a></h2>';
		
		}

		?>
		<form method="post" id="createNews">
			<label for="title">Overskrift:</label>
				<input type="text" name="title" id="title" placeholder="Julen kommer tidligt">
			<label for="news">nyheden:</label>
			<textarea form="createNews" id="news" name="news"></textarea>
			<input type="submit" value="send nyhed">
		</form>
	</body>
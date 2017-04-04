<?php


	require("core/rssxmlClass.php");
	require("core/config.php");
	$link = "http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/rss/opg_3/news.php?id=";
	try {
	$array = array("newsLink" => "http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/rssxml/opg_3/news.php", "newsTable" => "dynweb__news", "siteName" => "RKM - Nyheder", "siteLink" => "http://runm17.wi4.sde.dk/projects/teorifag/dynamisk_web/rssxml/opg_3/index.php", "siteDesc" => "Mit nyheds system med RSS FEED læser.");
	$obj = new FeedCreator($mysqli);
	$obj->setAll($array);
	$obj->createRSS();
	}
	catch (Exception $e) {
		echo "problem loading FeedCreator: ".$e->getMessage();
	}
?>
<?php
	require("core/functions.php");
	$table = get_Table("dynweb__news", array("id", "title", "date"), "WHERE 1 order by ID DESC");
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Dynamisk web OPG RSS 3</title>
	</head>
	
	<body>
		<h2>administration</h2>
		<a href="create.php" title="opret nyhed">Opret nyhed</a>
		<a href="xml/feed.xml" title="rss feed">Rss feed</a>
		
		<h2>Aktuelle nyheder</h2>
<ol>
		<?php
		foreach($table as $val) {
			echo '<li><a href="news.php?id='.$val["id"].'">'.$val["title"].'</a></li>';
		}		
		?>
</ol>	
</body>
</html>
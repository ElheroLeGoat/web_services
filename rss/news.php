<?php

	$id = $_GET["id"];
	require("core/functions.php");
	$validated = validateString($id, "id", "int", 0, 0);
	$id = $validated["modified"];
	$table = get_Table("dynweb__news", array("title", "news", "date"), "WHERE id=".$id." order by ID DESC");
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Dynamisk web OPG RSS 3</title>
	</head>
	
	<body>		
		<a href="index.php">Gå til forsiden</a>
		<h1><?php echo $table[0]["title"];?></h1>

		<p><?php echo $table[0]["news"]; ?></p>

		<p>oprettet: <?php echo $table[0]["date"]; ?> </p>
</body>
</html>
<html>
	<head>
	<title>Success</title>
	<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
	</head>

<?php

$file = file_get_contents("xml/".$_GET["fn"].".xml");
echo '<h1><a href="index.php">Go back</a></h1>';

echo "<h2>Haven't spent much time, but it seems like you managed to do it.</h2>";

echo "<h3>following file was created:</h3>";

echo "<p>Showing content of <a href=\"xml/".$_GET["fn"].".xml\"><b>xml/".$_GET["fn"].".xml</b></a></p>";

echo "<pre class=\"prettyprint lang-xml\">";
echo htmlentities($file);	
	
echo "</pre>";


?>
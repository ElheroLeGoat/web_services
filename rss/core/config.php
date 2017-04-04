<?php
	mb_internal_encoding("UTF-8");
	$host = "localhost";
	$username = "runm17.wi4";
	$password = "4ykpk3p2";
	$database = "runm17_wi4_sde_dk";
	


	$mysqli = new mysqli($host, $username, $password, $database);

	if ($mysqli->connect_errno) {
		die("Couldn't connect to". $mysqli->connect_errno ." ". $mysqli->connect_error);
	}
	if (!$mysqli->set_charset("utf8")) {
		echo printf("Error couldn't load character set utf8: %s\n", $mysqli->error);
		exit();
	}

<html>
	<head>
		<meta charset="utf-8">
		<title>XML dummy generator</title>
	</head>
	<body>
		<form action="generator.php" method="post">
		<select name="case">
			<option value="1">Generate Country xml without country code</option>
			<option value="2">Generate Country xml with country code</option>
		</select>
		<label for="name">Indtast Filnavn (brug ikke .xml)</label>
		<input type="text" name="name" id="name">
		<input type="submit" value="generate xml sheet">
		</form>
	</body>
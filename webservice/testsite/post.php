<html>
    <head>
        <meta charset="utf-8">
        <title>Welcome to the Webservice Tester</title>
        <style>
            form {
                display:flex;
                flex-direction:column;
                max-width:250px;
                font-family:sans-serif;
            }
            form input {
                border-radius:10px;
                outline:none;
                border:2px solid #000;
                margin:5px 0px;
                padding:10px;
                background-color:#efefef;
                font-weight:bold;
            }
        </style>
    </head>
    
    <body>
        <form method="POST" action="city/create/">
            <label for="zipcode">Postal code (max 4 number):</label>
            <input type="text" id="zipcode" name="zipcode" maxlength="4" minlength="4" required>
            <label for="cityname">City name:</label>
            <input type="text" id="cityname" name="cityname" minlength="5" maxlength="75" required>
            <label for="countrycode">Country code (eg. US, DK, DE):</label>
            <input type="text" id="countrycode" name="countrycode" maxlength="2" minlength="2"  required>
            <input type="submit" value="create city">
        </form>
		<a href="city/getall">Vis alle byer</a>
		<a href="country/getall">Vis alle lande</a>
	</body>
    
       
</html>
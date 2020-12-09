<?php

	$adresse = isset($_GET["adresse"]) ? strval($_GET["adresse"]) : '1645 route des lucioles Biot';
	
	$adresse_url = "http://nominatim.openstreetmap.org/search?format=json&polygon=1&addressdetails=1&q=".urlencode($adresse);
	
	$opts = array('http'=>array('header'=>"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.112 Safari/535.1 \r\n"));
    
    $context = stream_context_create($opts);
    
    $jsonBrut = file_get_contents($adresse_url,false, $context);
	
	$jsonDecode = json_decode($jsonBrut);
	
	foreach($jsonDecode as $area){
	
		$lat = $area->lat; 
		$lon = $area->lon;
		$boundingbox = $area->boundingbox; 
	
		$urlMeteo = "http://api.openweathermap.org/data/2.5/weather?lang=fr&units=metric&lat=$lat&lon=$lon&APPID=1dc250956d9b1464e48dd58f63d25cfb";
		
		$meteoBrut = file_get_contents($urlMeteo,false, $context);
		$meteoDecode = json_decode($meteoBrut);
	


		$nom = $meteoDecode->name;
		$icon = $meteoDecode->weather[0]->icon;
		$urlIcon = "http://openweathermap.org/img/w/".$icon.".png";
		$temp = $meteoDecode->main->temp;
		$description = $meteoDecode->weather[0]->description;
		
		$html = $html.<<<EOD
	
		<section>
		<iframe style="border: none;box-shadow: 1px 1px 3px black;float: left; margin: 0 2em 2em 0;width:600px; height:480px;"
			src="http://www.openstreetmap.org/export/embed.html?bbox=$boundingbox[2]%2C$boundingbox[0]%2C$boundingbox[3]%2C$boundingbox[1]&amp;layer=mapnik"></iframe>
		<p>Le temps à $nom : </p>
		<img src="$urlIcon">
		<p>Température de $temp °C, $description</p>
		</section>
		
		EOD;
		
	}


?>

<!doctype html>

<html lang ="fr">

	<head>

		<meta charset="utf-8">
		<title>tp10 php</title> 
		
		<!-- licence Creative Commons Attribution-ShareAlike (CC-BY-SA) -->
	
	</head>

	<body>
		
		<form id ='formu' action = "" method="get">
				<label for='adresse'>Saissez l'adresse:</label>
				<input type='text' id='adresse' name='adresse' required/>
		</form>
		
		<?php
		
			echo $html;
		
		?>
		
	</body>

</html>
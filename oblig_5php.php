<?php
include_once(connection.php);

public function getNode($el, $tag){
  return $el->getElementsByTag($tag)->textContent;
}
// Create DOM Document
$doc = new DOMDocument;

// Create PDO connection
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
                           DB_USER, DB_PWD,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

// Prevent white spaces
$doc->preserveWhiteSpace = false;
// Load XML file
$doc->Load('skiersLog.xml');

// Setup xpath connection to DOM Document ($doc)
$xpath = new DOMXPath($doc);

//Import skiers

$query = '/Skiers/Skier';
$elements =  $xpath->query($query);

if (!is_null($elements)){
//Preparing PDO INSERT Statment
$stmt = $db->prepare("INSERT INTO skiers(userName, firstName, lastName, yearOfBirth) VALUES(?,?,?,?)");
  foreach ($elements as $el) {
    $stmt->bindValue(1, $el->getAttribute('userName')->textContent , PDO::PARAM_STR);
    $stmt->bindValue(2, getNode($el, 'FirstName') , PDO::PARAM_STR);
    $stmt->bindValue(3, getNode($el, 'LastName'), PDO::PARAM_STR);
    $stmt->bindValue(4, getNode($el, 'YearOfBirth'), PDO::PARAM_INT);
    $stmt->execute();
  }

//Import Clubs
$query = '//Clubs/Club';
$elements = $xpath->query($query);

if (!is_null($elements)){
//Preparing PDO INSERT Statment
  $stmt = $db->prepare("INSERT INTO clubs(ID, name, city, county) VALUES (?,?,?,?)");
  foreach ($elements as $el) {

    $stmt->bindValue(1, $el->getAttribute('id')->textContent, PDO::PARAM_STR);
    $stmt->bindValue(2, getNode($el, 'Name'), PDO::PARAM_STR);
    $stmt->bindValue(3, getNode($el, 'City'), PDO::PARAM_STR);
    $stmt->bindValue(4, getNode($el, 'County'), PDO::PARAM_STR);
    $stmt->execute();
  }
}

//Import Seasons
$query = '//Season';
$elements = $xpath->query($query);

if (!is_null($elements)){
  
  foreach ($elements as $season) {
	$temp = $season->getAttribute('fallYear')->textContent;
	$stmt = $db->prepare("INSERT INTO season(season, clubID, skierUserName, skierTotalDistance) VALUES (?,?,?,?)");
    $stmt->bindValue(1, $temp, PDO::PARAM_STR);
	$query = './Skiers';
	$season = $xpath->query($query);
	foreach ($season as $Skiers){
		$temp = $skiers->getAttribute('clubID')->textContent;
		$stmt->bindValue(2, $temp, PDO::PARAM_STR);
		$query = './Skier';
		$Skiers = $xpath->query($query);
		foreach ($Skiers as $skier){
			$temp = $skier->getAttribute('userName')->textContent;
			$totdist = 0;
			$stmt->bindValue(3, $temp, PDO::PARAM_STR);
			$query = './Log/Entry';
			$Skier = $xpath->query($query);
			foreach ($Log as $Entry){
				$totdist+=getNode($Entry, 'Distance');
				$logstmt = $db->prepare("INSERT INTO log(skierUserName, entryDate, area, distance) VALUES (?,?,?,?)");
				$logstmt->bindValue(1, $temp, PDO::PARAM_STR);
				$logstmt->bindValue(2, getNode($entry, 'Date'), PDO::PARAM_STR);
				$logstmt->bindValue(3, getNode($entry, 'Area'), PDO::PARAM_STR);
				$logstmt->bindValue(4, getNode($entry, 'Distance'), PDO::PARAM_STR);
				$logstmt->execute();

			}	
		$stmt->bindValue(4, $totdist, PDO::PARAM_STR);
		}
		
	}
	$stmt->execute();
  }
}

}
 ?>
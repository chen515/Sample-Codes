<?php
require_once("mysql_db.php");

$mydb = new MySqlDB("localhost", "", "","test");
$myColor = $_POST["color"];
$sql = "SELECT sum(Vote)Vote FROM Votes where Color='$myColor'";
$results = $mydb->query($sql);
$myRow = $mydb->fetchAssoc($results);

//build return data for ajax
if(count($myRow))
  echo $myRow['Vote'];
?>

<?php
function DBconnect(){

//$servername = "localhost";
//$username = "itayrm_ItayRam";
//$password = "itay0547862155";
//$dbname = "itayrm_dogs_boarding_house";
//$conn = new mysqli($servername, $username, $password, $dbname);

$host="127.0.0.1";
$port=3306;
$socket="";
$user="root";
$password="";
$dbname="itayrm_dogs_boarding_house";
$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());
return $conn;

}
?>
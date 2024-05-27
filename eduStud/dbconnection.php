<?php 
$server="localhost";
$user="root";
$password="";
$dbname="studentdb2";
$conn=new mysqli($server,$user,$password,$dbname);
if ($conn->connect_error)
 {
die(": " . $conn->connect_error);
 }
else
 {
echo "";
 }
?>

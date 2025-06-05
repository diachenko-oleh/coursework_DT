<?php 
$host = "localhost";      
$port = "5432";            
$dbname = "Library";      
$user = "postgres";      
$password = "1234"; 

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Помилка підключення до бази даних");
}
?>
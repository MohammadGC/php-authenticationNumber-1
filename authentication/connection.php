<?php

$servername = "localhost";
$dbname = "authentication";
$username = "root";
$password = "";

global $pdo;
//connect to the database whith Pdo connection
try {
    $option = [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC];//show error and Reading information by the Assoc
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password,$option);
    
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
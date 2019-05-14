<?php
//Controleer als er al een sessie is aangemaakt.
if(!isset($_SESSION)) {
    session_start();
}

$servername = "plantagerpdb.mysql.db";
$dbname = "plantagerpdb";
$username = "plantagerpdb";
$password = "N094v4zm3orr";

try {
    $conn = new PDO("mysql:host=$servername;charset=utf8;dbname=" . $dbname, $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
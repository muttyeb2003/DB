<?php
$host = 'dbcourse.cs.smu.ca';                
$user = 'ux';         
$pass = 'your password';         
$db   = 'uxx'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
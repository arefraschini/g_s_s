<?php
//Step1
try {
    $db = mysqli_connect('localhost','scorer','scorerPwd','games')
    or die('Error connecting to MySQL server.');
//    $db.set
    
//$conn = new PDO("mysql:host=localhost; dbname=games", 'scorer','scorerPwd');
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Connection OK - " . $db->ping();
} catch (PDOException $e) {
    echo "Connection KO: " . $e->getMessage();
}
?>

<html>
 <head>
 </head>
 <body>
 <h1>PHP connected to MySQL</h1>
 <?php phpinfo(); ?>
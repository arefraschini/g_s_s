<?php
session_start();
$dbConn = new mysqli('localhost','scorer','scorerPwd','games')
or die('Error connecting to MySQL server.');
$name = $_POST["myName"];
$pwd = $_POST["myPwd"];
$result = $dbConn->query("SELECT passwd FROM utenti WHERE profilo = '" . $name . "'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row["passwd"] == $pwd) {
        // Do the stuff...
        echo "44 gatti";
    } else {
        $_REQUEST['errMsg'] = "errore passwd!!";
        header("Location: login.php");
        die();
    }
} else {
    $_REQUEST['errMsg'] = "errore utente!!";
    header("Location: login.php");
    die();
}
header("/");
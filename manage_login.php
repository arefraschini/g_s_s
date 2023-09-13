<?php
include "./conf/db.php";
include "./include/db.php";

session_start();

// clear session after logout
function clearSession(){
	unset($_SESSION["current_user"]);
	clearMessages();
	// TODO : add here other variables to unset
}

// clear session messages
function clearMessages(){
	unset($_SESSION["errMessage"]);
	unset($_SESSION["message"]);
	// TODO : add here other variables to unset
}

if ( isset($_POST["action"]) && $_POST["action"] == "login") {
	$name = $_POST["myName"];
	$pwd = $_POST["myPwd"];

	$dbConn=getConnection($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME); //return mysqli connection or null

	$result = $dbConn->query("SELECT passwd FROM utenti WHERE profilo = '" . mysqli_real_escape_string($dbConn,$name). "'");
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		if (($row["passwd"] == md5($pwd)) || ($row["passwd"] == $pwd)) {
			// Do the stuff
		    $_SESSION['message'] = "Benvenuto '".$name."'. ";
			$_SESSION['current_user'] = $name;
			header("Location: home.php");
		} else {
		    $_SESSION['errMessage'] = "Prego verificare credenziali.";
			header("Location: index.php");
			die();
		}
	} else {
	    $_SESSION['errMessage'] = "Prego verificare nome utente e password.";
		header("Location: index.php");
		die();
	}
}else if ( isset($_GET["action"]) && $_GET["action"] == "logout") {
	$curr_user=$_SESSION["current_user"]; //salvo il nome che poi perdero'
	clearSession();
	$_SESSION["message"] = "Utente '".$curr_user."' disconnesso. ";
	header("Location: index.php");
	die();
}	


header("/");
?>
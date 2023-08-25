<?php namespace gsas;
include "./include/check_session.php";
include "./conf/db.php";
include "./include/db.php";
// Gestione chiamate Ajax
$data = json_decode(file_get_contents("php://input"), true);
//var_dump($_POST);

//if (isset($_POST) && (isset($_POST["saveNewPlayer"]) || isset($_POST["readPlayersTeamId"]))) {
if (isset($data) && (isset($data["saveNewPlayer"]) || isset($data["readPlayersTeamId"]))) {
	$dbConn=getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	if (isset($data["saveNewPlayer"])) {
		$playerTeamId = $data["playerTeamId"];
		$playerNumber = $data["newNumber"];
		$playerName = mysqli_real_escape_string($dbConn, $data["newName"]);
		$playerSurname = mysqli_real_escape_string($dbConn, $data["newSurname"]);
		$sqlPlayer = "INSERT INTO players (cognome, nome, teamId, numeroMaglia) VALUES('"
			. $playerSurname . "', '" . $playerName . "', " . $playerTeamId
			. ", " . $playerNumber . ");";
			$resSql = $dbConn->query($sqlPlayer);
			if ($resSql) {
				$resSql = $dbConn->query("SELECT MAX(playerId) as maxId FROM players;");
				$resSet = $resSql->fetch_assoc();
				$maxId = $resSet["maxId"];
				$feedbackData = array("isOk"=>"Y", "msg"=>"Saved!", "maxId"=>$maxId);
			} else {
				$feedbackData = array("isOk"=>"N", "msg"=>"NOT SAVED - err: " . $dbConn->errorno);
			}
			echo (json_encode($feedbackData));
			exit;
	} else  if (isset($data["readPlayersTeamId"])) {
		$teamId = $data["readPlayersTeamId"];
		$sqlReadPlayers = "SELECT playerId, cognome, nome, numeroMaglia FROM players WHERE teamId = " . $teamId
		. " ORDER BY numeroMaglia;";
		$players = array();
		$resSql = $dbConn->query($sqlReadPlayers);
		//  array_push($players, $resSql->num_rows);
		if ($resSql->num_rows > 0) {
			while ($resSet = $resSql->fetch_assoc()) {
				$singlePlayer = array();
				$singlePlayer["playerId"] = $resSet["playerId"];
				$singlePlayer["nome"] = $resSet["nome"];
				$singlePlayer["cognome"] = $resSet["cognome"];
				$singlePlayer["numeroMaglia"] = $resSet["numeroMaglia"];
				array_push($players, $singlePlayer);
			}
		}
		echo (json_encode($players));
		exit;
	}
}
?>
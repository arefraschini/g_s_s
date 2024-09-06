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
				$feedbackData = array("isOk"=>"N", "msg"=>"NOT SAVED - err: " . $dbConn->error. "SQL=".$sqlPlayer);
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

if (isset($data) && (isset($data["gamePlayerDetails"]))) {
	$dbConn=getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	// Check even if there is only a single branch but the next step will generate new branches...
	if (isset($data["gamePlayerDetails"])) {
		$gameId = $data["gameId"];
		$playerId = $data["playerId"];
		$sqlReadGameData = "SELECT *, (tl_ok + (2 * p2_ok) + (3 * p3_ok)) AS points FROM playersingame WHERE gameId = " . $gameId . " AND playerId = " . $playerId . ";";
		/*
		echo $sqlReadGameData;
		exit;
		*/
		$playerData = array();
		$resSql = $dbConn->query($sqlReadGameData);
		if ($resSql->num_rows > 0) {
			$resSet = $resSql->fetch_assoc();
			$playerData["playerId"] = $resSet["playerId"];
			$playerData["fouls"] = $resSet["fouls"];
			$playerData["tl"] = $resSet["tl"];
			$playerData["tl_ok"] = $resSet["tl_ok"];
			$playerData["p2"] = $resSet["p2"];
			$playerData["p2_ok"] = $resSet["p2_ok"];
			$playerData["p3"] = $resSet["p3"];
			$playerData["p3_ok"] = $resSet["p3_ok"];
			$playerData["rimbA"] = $resSet["rimbA"];
			$playerData["rimbD"] = $resSet["rimbD"];
			$playerData["palPer"] = $resSet["palPer"];
			$playerData["palRec"] = $resSet["palRec"];
			$playerData["points"] = $resSet["points"];
			$playerData["msg"] = "OK";
			echo (json_encode($playerData));
			exit;
		} else {;
			$playerData["msg"] = "KO - No data found";
			echo (json_encode($playerData));
			exit;
		}
	}
}

if (isset($data) && (isset($data["setPlayerAsOnTheField"]) || isset($data["pointsForPlayer"])
					|| isset($data["dataForPlayer"]) || isset($data["quarterOfGame"])
					|| isset($data["personalFouls"]))) {
	$dbConn=getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	$gameId = $data["gameId"];
	if (isset($data["quarterOfGame"])) {
		$q = $data["quarter"];
		$resSql = $dbConn->query("UPDATE games SET quarter = '" . $q . "' WHERE gameId = " . $gameId);
		$playerData = array();
		if ($resSql) {
			$playerData["msg"] = "OK";
		} else {
			$playerData["msg"] = "KO - Some error on setting current quater of the game #" . $gameId;
		}
		echo (json_encode($playerData));
		exit;
	}
	$playerId = $data["playerId"];
	$sqlWherePlayerInGamesClause = " WHERE gameId = " . $gameId . " AND playerId = " . $playerId . ";";
	// Check even if there is only a single branch but the next step will generate new branches...
	if (isset($data["setPlayerAsOnTheField"])) {
		$setPlayerAsOnTheField = $data["setPlayerAsOnTheField"];
		$sqlSetAsOnTheField = "UPDATE playersingame SET onTheField = " . $setPlayerAsOnTheField . $sqlWherePlayerInGamesClause;
		/*
		 echo $sqlReadGameData;
		 exit;
		 */
		$playerData = array();
		$resSql = $dbConn->query($sqlSetAsOnTheField);
		if ($resSql) {
			$playerData["msg"] = "OK";
		} else {
			$playerData["msg"] = "KO - Player not set as \"on the field\"";
		}
		echo (json_encode($playerData));
		exit;
	} else if (isset($data["pointsForPlayer"])) {
		// "dbField": dbField, "made": (isOk ? 1 : 0)
		$dbFieldRoot = $data["dbField"];
		$made = $data["made"];
		$teamLetter = $data["teamLetter"];
		$idTeam = $data["idTeam"];
		$setPointsSQL = "UPDATE playersingame"
						. " SET " . $dbFieldRoot . " = " . $dbFieldRoot . " + 1, " . $dbFieldRoot . "_ok = " . $dbFieldRoot . "_ok + " . $made
						. $sqlWherePlayerInGamesClause;
		$playerData = array();
		$resSql = $dbConn->query($setPointsSQL);
		if ($resSql) {
			$playerData["msg"] = "OK";
			$sqlReadPlayerScore = "SELECT (tl_ok + (2 * p2_ok) + (3 * p3_ok)) AS points FROM playersingame WHERE gameId = " . $gameId . " AND playerId = " . $playerId . ";";
			$resPts = $dbConn->query($sqlReadPlayerScore);
			$resSet = $resPts->fetch_assoc();
			$playerData["points"] = $resSet["points"];
			$sqlReadTeamScore = "SELECT p.teamId AS team, (SUM(pig.tl_ok) + 2*SUM(pig.p2_ok) + 3*SUM(pig.p3_ok)) AS points"
								. " FROM	playersingame AS pig"
								. " INNER JOIN players AS p ON pig.playerId = p.playerId"
								. " WHERE pig.gameId = " . $gameId . " AND p.teamId = " . $idTeam
								. " GROUP BY p.teamId;" ;
			$resPts = $dbConn->query($sqlReadTeamScore);
			$resSet = $resPts->fetch_assoc();
			$playerData["teamPoints"] = $resSet["points"];
			$playerData["teamLetter"] = $teamLetter;
		} else {
			$playerData["msg"] = "KO - Some error on setting points to the player #" . $playerId;
		}
		echo (json_encode($playerData));
		exit;
	} else if (isset($data["dataForPlayer"])) {
		$dbFieldRoot = $data["dbField"];
		$teamLetter = $data["teamLetter"];
		$idTeam = $data["idTeam"];
		$setPointsSQL = "UPDATE playersingame SET " . $dbFieldRoot . " = " . $dbFieldRoot . " + 1"
						. $sqlWherePlayerInGamesClause;
		$playerData = array();
		$resSql = $dbConn->query($setPointsSQL);
		if ($resSql) {
			$playerData["msg"] = "OK";
			$playerData["teamLetter"] = $teamLetter;
		} else {
			$playerData["msg"] = "KO - Some error on setting data to the player #" . $playerId;
		}
		echo (json_encode($playerData));
		exit;
	} else if (isset($data["personalFouls"])) {
		$foul = $data["foulType"];
		$allFouls = $data["regFouls"];
		$op = $data["operation"];
		if (($foul != "F" && $foul != "T" && $foul != "U")
			|| ($op != "APPEND" && $op != "REM_LAST")) {
				echo $foul . "|" . $allFouls . "|" . $op;
				exit;
			}
		$value = "'" . ($op == "APPEND" ? $allFouls . $foul : preg_replace("((.*)" . $foul . "(.*))", "$1$2", $allFouls)) . "'";
		$sql = "UPDATE playersingame SET fouls = " . $value . $sqlWherePlayerInGamesClause;
		$playerData = array();
		$resSql = $dbConn->query($sql);
		if ($resSql) {
			$playerData["msg"] = "OK";
			$playerData["fouls"] = str_replace("'", "", $value);
		} else {
			$playerData["msg"] = "KO - Some error on setting data to the player #" . $playerId;
		}
		echo (json_encode($playerData));
		exit;
	}
}
	?>
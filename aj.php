<?php namespace gsas;
include "./include/check_session.php";
include "./conf/db.php";
include "./include/db.php";
// Gestione chiamate Ajax
var_dump($_POST);

if (isset($_POST) && (isset($_POST["saveNewPlayer"]) || isset($_POST["readPlayersTeamId"]))) {
    $dbConn=getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if (isset($_POST["saveNewPlayer"])) {
        $playerTeamId = $_POST["teamIdForNewPlayer"];
        $playerNumber = $_POST["newNumber"];
        $playerName = mysqli_real_escape_string($dbConn, $_POST["newName"]);
        $playerSurname = mysqli_real_escape_string($dbConn, $_POST["newSurname"]);
        $sqlPlayer = "INSERT INTO players (cognome, nome, teamId, numeroMaglia) VALUES('"
            . $playerSurname . "', '" . $playerName . "', " . $playerTeamId
            . ", " . $playerNumber . ");";
            $resSql = $dbConn->query($sqlPlayer);
            if ($resSql) {
                $feedbackData = array("isOk"=>"Y", "msg"=>"Saved!");
            } else {
                $feedbackData = array("isOk"=>"N", "msg"=>"NOT SAVED - err: " . $dbConn->errorno);
            }
            echo (json_encode($feedbackData));
            exit;
    } else  if (isset($_POST["readPlayersTeamId"])) {
        $teamId = $_POST["readPlayersTeamId"];
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
        echo (json_encode(array("res"=>$players)));
        exit;
    }
}
?>
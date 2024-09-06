<?php namespace gsas;
include "./include/check_session.php";
include "./conf/db.php";
include "./include/db.php";

if (isset($_POST) && (isset($_POST["trgr"]))) {
	$dbConn = getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	if (isset($_POST["gameId"])) {
		$theGameId = $_POST["gameId"];
	}
	if (isset($_POST["aTeamId"])) {
		$idTeamA = $_POST["aTeamId"];
	}
	if (isset($_POST["bTeamId"])) {
		$idTeamB = $_POST["bTeamId"];
	}
	if (isset($_POST["gameStatus"])) {
		$gameStatus = $_POST["gameStatus"];
	}
	if (isset($_POST["aPlayers"])) {
		$aCodes = $_POST["aPlayers"];
	}
	if (isset($_POST["bPlayers"])) {
		$bCodes = $_POST["bPlayers"];
	}
	$isNewGame = ($gameStatus == "N");
	if ($isNewGame) {
		$sqlPlayers = "INSERT INTO playersingame (gameId, playerId) SELECT " . $theGameId . " AS aGameId, playerId FROM players WHERE playerId IN ("
						. $aCodes . ", " . $bCodes . ");";
		$dbConn->query($sqlPlayers);
		$dbConn->query("UPDATE games SET stato = 'G' WHERE gameId = " . $theGameId);
	} else {
		$result = $dbConn->query("SELECT teamA, teamB FROM games WHERE gameId = " . $theGameId);
		$resSet = $result->fetch_assoc();
		$idTeamA = $resSet["teamA"];
		$idTeamB = $resSet["teamB"];
	}
	// Get current game score
	$scoreSQL = "SELECT t.teamId AS team, (SUM(pig.tl_ok) + 2*SUM(pig.p2_ok) + 3*SUM(pig.p3_ok)) AS points"
				. " FROM	playersingame AS pig"
				. " INNER JOIN players AS p ON pig.playerId = p.playerId"
				. " INNER JOIN teams AS t ON p.teamId = t.teamId"
				. " WHERE pig.gameId = " . $theGameId . " GROUP BY t.teamId;";
	$result = $dbConn->query($scoreSQL);
	$tAScore = 0;
	$tBScore = 0;
	while ($resSet = $result->fetch_assoc()) {
		if ($resSet["team"] == $idTeamA)
			$tAScore = $resSet["points"];
		else
			$tBScore = $resSet["points"];
	}
	// Get current game data
	$result = $dbConn->query("SELECT g.gamePlace AS gGamePlace, g.gameDate AS gGameDate, g.gameTime AS gGameTime, coalesce(g.quarter, '') AS qt, " .
							"tA.nomeTeam AS tAteam, tB.nomeTeam AS tBteam FROM games AS g " .
							"INNER JOIN teams AS tA ON g.teamA = tA.teamId " .
							"INNER JOIN teams AS tB ON g.teamB = tB.teamId " .
							"WHERE g.gameId = " . $theGameId);
	while ($resSet = $result->fetch_assoc()) {
		$gameTitle = $resSet["gGamePlace"] . " @ " . $resSet["gGameDate"] . " " . $resSet["gGameTime"];
		$teamAname = $resSet["tAteam"];
		$teamBname = $resSet["tBteam"];
		$curQt = $resSet["qt"];
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Game # <?php echo $theGameId . " &ndash; " . $gameTitle ?></title>
	<link type="text/css" rel="stylesheet" href="gsas.css" />
	<script src="fnct.js"></script>
</head>
<body>
<h2><?php echo $gameTitle ?></h2>
<!--
<div class="upperWholeScoreTable">
	<h2><?php echo $gameTitle ?></h2>
	<div class="teamA dx score">
		<p id="team_<?php echo $idTeamA ?>"><?php echo $teamAname ?></p>
	</div>
	<div class="main score">
		<p class="points"><span id="teamA"><?php echo $tAScore ?></span> - <span id="teamB"><?php echo $tBScore ?></span></p>
		<p class="quarters"><span id="q1"<?php if ($curQt == "q1") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">1</span><span id="q2"<?php if ($curQt == "q2") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">2</span><span id="q3"<?php if ($curQt == "q3") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">3</span><span id="q4"<?php if ($curQt == "q4") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">4</span><span id="ot1"<?php if ($curQt == "ot1") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">ot1</span><span id="ot2"<?php if ($curQt == "ot2") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">ot2</span></p>
	</div>
	<div class="teamB dx score">
		<p id=	"team_<?php echo $idTeamB ?>"><?php echo $teamBname ?></p>
	</div>
</div>
-->
<div class="wholeScoreTable">
	<div class="teamA dx score">
		<p id="team_<?php echo $idTeamA ?>"><?php echo $teamAname ?></p>
		<ul>
		<?php
			$sqlPlayers = $isNewGame
							? "SELECT *, '--' AS points, FALSE AS onTheField FROM players WHERE playerId IN (" . $aCodes . ") ORDER BY numeroMaglia;"
							: "SELECT p.*, (tl_ok + (2 * p2_ok) + (3 * p3_ok)) AS points, onTheField FROM playersingame AS pig INNER JOIN games AS g ON g.gameId = pig.gameId "
								. "INNER JOIN players AS p ON p.playerId = pig.playerId "
								. "WHERE p.teamId = " . $idTeamA . " ORDER BY pig.onTheField DESC, p.numeroMaglia";
			$result = $dbConn->query($sqlPlayers);
			while ($resSet = $result->fetch_assoc()) {
				$plId = $resSet["playerId"];
				$teamLetter = "'A'";
				$idTeam = $idTeamA;
				$isOnTheField = $resSet["onTheField"];
			?>
				<li>
					<div class="playerHeader"><span id="_<?php echo $plId ?>" onClick="showDetails(<?php echo $plId . ", " . $theGameId . ", " . $idTeam ?>)"><?php echo $resSet["numeroMaglia"] ?> &ndash; <?php echo $resSet["cognome"] ?>, <?php echo $resSet["nome"] ?></span><span class="points" id="pts_<?php echo $plId ?>"><?php echo $resSet["points"] ?></span><div class="onTheGround"><button class="ok""<?php if ($isOnTheField) { ?> style="display: none;"<?php } ?> id="in_<?php echo $plId?>" onClick="showBtns(this, <?php echo $plId . ", " . $theGameId ?>)">IN</button><button class="ko""<?php if (!$isOnTheField) { ?> style="display: none;"<?php } ?> id="out_<?php echo $plId?>" onClick="hideBtns(this, <?php echo $plId . ", " . $theGameId ?>)">OUT</button></div></div>
					<div class="actions"<?php if (!$isOnTheField) { ?> style="display: none;"<?php } ?>><button class="ok" onClick="setOkPts(<?php echo $plId . ", " . $theGameId . ", 1, " . $teamLetter . ", " . $idTeam ?>)">1 ok</button><button class="ok" onClick="setOkPts(<?php echo $plId . ", " . $theGameId . ", 2, " . $teamLetter . ", " . $idTeam ?>)">2 ok</button><button class="ok" onClick="setOkPts(<?php echo $plId . ", " . $theGameId . ", 3, " . $teamLetter . ", " . $idTeam ?>)">3 ok</button><button class="ok" onClick="setRBAtt(<?php echo $plId . ", " . $theGameId . ", 'rimbA', " . $teamLetter . ", " . $idTeam ?>)">RB Att</button><button class="ok" onClick="setPRec(<?php echo $plId . ", " . $theGameId . ", 'palRec', " . $teamLetter . ", " . $idTeam ?>)">PRec</button></div>
					<div class="actions"<?php if (!$isOnTheField) { ?> style="display: none;"<?php } ?>><button class="ko" onClick="setKoPts(<?php echo $plId . ", " . $theGameId . ", 1, " . $teamLetter . ", " . $idTeam ?>)">1 ko</button><button class="ko" onClick="setKoPts(<?php echo $plId . ", " . $theGameId . ", 2, " . $teamLetter . ", " . $idTeam ?>)">2 ko</button><button class="ko" onClick="setKoPts(<?php echo $plId . ", " . $theGameId . ", 3, " . $teamLetter . ", " . $idTeam ?>)">3 ko</button><button class="ok" onClick="setRBDif(<?php echo $plId . ", " . $theGameId . ", 'rimbD', " . $teamLetter . ", " . $idTeam ?>)">RB Dif</button><button class="ko" onClick="setPPer(<?php echo $plId . ", " . $theGameId . ", 'palPer', " . $teamLetter . ", " . $idTeam ?>)">PPer</button></div>
				</li>
		<?php } ?>
		</ul>
	</div>
	<div class="main score">
		<p class="points"><span id="teamA"><?php echo $tAScore ?></span> - <span id="teamB"><?php echo $tBScore ?></span></p>
		<p class="quarters"><span id="q1"<?php if ($curQt == "q1") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">1</span><span id="q2"<?php if ($curQt == "q2") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">2</span><span id="q3"<?php if ($curQt == "q3") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">3</span><span id="q4"<?php if ($curQt == "q4") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">4</span><span id="ot1"<?php if ($curQt == "ot1") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">ot1</span><span id="ot2"<?php if ($curQt == "ot2") { ?> class="active"<?php } ?> onClick="setQuarter(this.id, <?php echo $theGameId ?>)">ot2</span></p>
	</div>
	<div class="teamB dx score">
		<p id=	"team_<?php echo $idTeamB ?>"><?php echo $teamBname ?></p>
		<ul>
		<?php
			$sqlPlayers = $isNewGame
							? "SELECT *, '--' AS points, FALSE AS onTheField FROM players WHERE playerId IN (" . $bCodes . ") ORDER BY numeroMaglia;"
							: "SELECT p.*, (tl_ok + (2 * p2_ok) + (3 * p3_ok)) AS points, onTheField FROM playersingame AS pig INNER JOIN games AS g ON g.gameId = pig.gameId "
								. "INNER JOIN players AS p ON p.playerId = pig.playerId "
								. "WHERE p.teamId = " . $idTeamB . " ORDER BY pig.onTheField DESC, p.numeroMaglia";
			$result = $dbConn->query($sqlPlayers);
			while ($resSet = $result->fetch_assoc()) {
				$plId = $resSet["playerId"];
				$teamLetter = "'B'";
				$idTeam = $idTeamB;
				$isOnTheField = $resSet["onTheField"];
			?>
				<li>
					<div class="playerHeader"><span id="_<?php echo $plId ?>" onClick="showDetails(<?php echo $plId . ", " . $theGameId . ", " . $idTeam ?>)"><?php echo $resSet["numeroMaglia"] ?> &ndash; <?php echo $resSet["cognome"] ?>, <?php echo $resSet["nome"] ?></span><span class="points" id="pts_<?php echo $plId ?>"><?php echo $resSet["points"] ?></span><div class="onTheGround"><button class="ok""<?php if ($isOnTheField) { ?> style="display: none;"<?php } ?> id="in_<?php echo $plId?>" onClick="showBtns(this, <?php echo $plId . ", " . $theGameId ?>)">IN</button><button class="ko""<?php if (!$isOnTheField) { ?> style="display: none;"<?php } ?> id="out_<?php echo $plId?>" onClick="hideBtns(this, <?php echo $plId . ", " . $theGameId ?>)">OUT</button></div></div>
					<div class="actions"<?php if (!$isOnTheField) { ?> style="display: none;"<?php } ?>><button class="ok" onClick="setOkPts(<?php echo $plId . ", " . $theGameId . ", 1, " . $teamLetter . ", " . $idTeam ?>)">1 ok</button><button class="ok" onClick="setOkPts(<?php echo $plId . ", " . $theGameId . ", 2, " . $teamLetter . ", " . $idTeam ?>)">2 ok</button><button class="ok" onClick="setOkPts(<?php echo $plId . ", " . $theGameId . ", 3, " . $teamLetter . ", " . $idTeam ?>)">3 ok</button><button class="ok" onClick="setRBAtt(<?php echo $plId . ", " . $theGameId . ", 'rimbA', " . $teamLetter . ", " . $idTeam ?>)">RB Att</button><button class="ok" onClick="setPRec(<?php echo $plId . ", " . $theGameId . ", 'palRec', " . $teamLetter . ", " . $idTeam ?>)">PRec</button></div>
					<div class="actions"<?php if (!$isOnTheField) { ?> style="display: none;"<?php } ?>><button class="ko" onClick="setKoPts(<?php echo $plId . ", " . $theGameId . ", 1, " . $teamLetter . ", " . $idTeam ?>)">1 ko</button><button class="ko" onClick="setKoPts(<?php echo $plId . ", " . $theGameId . ", 2, " . $teamLetter . ", " . $idTeam ?>)">2 ko</button><button class="ko" onClick="setKoPts(<?php echo $plId . ", " . $theGameId . ", 3, " . $teamLetter . ", " . $idTeam ?>)">3 ko</button><button class="ok" onClick="setRBDif(<?php echo $plId . ", " . $theGameId . ", 'rimbD', " . $teamLetter . ", " . $idTeam ?>)">RB Dif</button><button class="ko" onClick="setPPer(<?php echo $plId . ", " . $theGameId . ", 'palPer', " . $teamLetter . ", " . $idTeam ?>)">PPer</button></div>
				</li>
		<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>
<div class="overlay" id="detailsOverlay">
	<div>
		<div class="overlayHeader"><p id="detPLayerTeam">squadra</p><button name="abcd" onclick="hideThis(this);">chiudi</button></div>
		<div class="playerDetails"><input type="hidden" id="hiddenPlayerIdDetails"/>
			<span class="detHeader" id="detPlName">nome giocatore</span>
			<div class="stats">
				<span>P.ti tot</span>
				<span id="detPlTot">1</span>
			</div>
			<div class="stats">
				<span>Falli</span>
				<span id="fouls">0</span>
				<div id="foulsPad">
					<div class="foulType"><span onClick="removeLast(<?php echo $theGameId . ", 'F'" ?>)">&ndash;</span> F <span onClick="append(<?php echo $theGameId . ", 'F'" ?>)">+</span></div>&nbsp;
					<div class="foulType"><span onClick="removeLast(<?php echo $theGameId . ", 'T'" ?>)">&ndash;</span> T <span onClick="append(<?php echo $theGameId . ", 'T'" ?>)">+</span></div>&nbsp;
					<div class="foulType"><span onClick="removeLast(<?php echo $theGameId . ", 'U'" ?>)">&ndash;</span> U <span onClick="append(<?php echo $theGameId . ", 'U'" ?>)">+</span></div>
				</div>
			</div>
			<div class="stats">
				<span>TL</span>
				<span id="tl">0</span>
			</div>
			<div class="stats">
				<span>TL ok</span>
				<span id="tl_ok">0</span>
			</div>
			<div class="stats">
				<span>T2p</span>
				<span id="p2">0</span>
			</div>
			<div class="stats">
				<span>T2p ok</span>
				<span id="p2_ok">0</span>
			</div>
			<div class="stats">
				<span>T3p</span>
				<span id="p3">0</span>
			</div>
			<div class="stats">
				<span>T3p ok</span>
				<span id="p3_ok">0</span>
			</div>
			<div class="stats">
				<span>Rimb Att</span>
				<span id="rimbA">0</span>
			</div>
			<div class="stats">
				<span>Rimb Dif</span>
				<span id="rimbD">0</span>
			</div>
			<div class="stats">
				<span>P perse</span>
				<span id="palPer">0</span>
			</div>
			<div class="stats">
				<span>P recup</span>
				<span id="palRec">0</span>
			</div>
		</div>
	</div>
</div>
</body>
</html>
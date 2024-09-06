<?php namespace gsas;
include "./include/check_session.php";
include "./conf/db.php";
include "./include/db.php";

$gameId = 0;
if (isset($_GET['gameId'])) {
	$gameId = $_GET['gameId'];
} else {
	exit;
}

$dbConn = getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
$sqlGame = "SELECT g.*, tA.teamId AS tAid, tB.teamId AS tBid, tA.nomeTeam AS tAnomeTeam, tB.nomeTeam AS tBnomeTeam,
tA.Campionato AS tAcampionato, tB.campionato AS tBcampionato FROM games AS g 
INNER JOIN teams AS tA ON g.teamA = tA.teamId
INNER JOIN teams AS tB ON g.teamB = tB.teamId
WHERE g.gameId = " . $gameId;
$result = $dbConn->query($sqlGame);
if ($result->num_rows > 0) {
	$resSet = $result->fetch_assoc();
	$tAid = $resSet["tAid"];
	$tBid = $resSet["tBid"];
	$tA = $resSet["tAnomeTeam"] . " - " . $resSet["tAcampionato"];
	$tB = $resSet["tBnomeTeam"] . " - " . $resSet["tBcampionato"];
	$gameName = $tA . " vs. " . $tB;
	$status = $resSet["stato"];
} else {
	exit;
}
$sqlTa = "SELECT playerId, cognome, nome, numeroMaglia FROM players WHERE teamId = " . $tAid . " ORDER BY numeroMaglia;";
$sqlTb = "SELECT playerId, cognome, nome, numeroMaglia FROM players WHERE teamId = " . $tBid . " ORDER BY numeroMaglia;";
$resTa = $dbConn->query($sqlTa);
$resTb = $dbConn->query($sqlTb);
?>
<html>
<head>
	<link type="text/css" rel="stylesheet" href="gsas.css" />
	<script type="text/javascript">
		function saveNewPlayer(aPrefix, aSelectId) {
			var teamId = document.getElementById("teamIdForNewPlayer" + aPrefix).value;
			// Save a new player
			var xmlhttp = new XMLHttpRequest();
	
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
					if (xmlhttp.status == 200) {
						// Read JSon data
						var resp = JSON.parse(xmlhttp.responseText);
						var currHeader = document.getElementById("newPlayerHeaderId" + aPrefix);
						currHeader.innerHTML = "New player" + " &ndash; " + resp.msg;
						currHeader.style.color = resp.isOk == "N" ? "red" : "green";
						if (resp.isOk != "N") {
							var num = document.getElementById("newNumber" + aPrefix);
							var name = document.getElementById("newName" + aPrefix);
							var surname = document.getElementById("newSurname" + aPrefix);
							var toAdd = "<option code='" + resp.maxId + "'>"
										+ num.value + " &ndash; "
										+ surname.value + ", " + name.value + "</option>";
							document.getElementById(aSelectId).innerHTML =
								document.getElementById(aSelectId).innerHTML + toAdd;
							num.value = "";
							name.value = "";
							surname.value = "";
						}
					} else if (xmlhttp.status == 400) {
						alert('There was an error 400');
					} else {
						alert('something else other than 200 was returned');
					}
				}
			};
	
			xmlhttp.open("POST", "aj.php", true);
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			var newNumber = document.getElementById("newNumber" + aPrefix).value;
			var newName = document.getElementById("newName" + aPrefix).value;
			var newSurname = document.getElementById("newSurname" + aPrefix).value;
	
			var newData = JSON.stringify({"playerTeamId": teamId, "newNumber": newNumber, "newName": newName, "newSurname": newSurname, "saveNewPlayer": "yess"});
			xmlhttp.send(newData);
			return;
		};

		function calcSelected(aSelect, aPrefix) {
			var cnt = 0;
			for (var i = 0; i < aSelect.options.length; i++){
				if(aSelect.options[i].selected){
					cnt++;
				}
			}
			document.getElementById("selectedPlayers" + aPrefix).innerHTML = cnt;
		}

		function isInteger(value) {
			if(parseInt(value,10).toString()===value) {
				return true;
			}
			return false;
		}

		function enableStartScoreBtn(aSet, aCheckedStatus) {
			if (aCheckedStatus) {
				var theOtherSet = (aSet == "A") ? "B" : "A";
				/*
					
					
				*/
				if (document.getElementById("selectionIsOK_" + theOtherSet).checked) {
					var numPlayers_Set = document.getElementById("selectedPlayers_" + theOtherSet).innerHTML;
					var numPlayers_OtherSet = document.getElementById("selectedPlayers_" + theOtherSet).innerHTML;
					document.getElementById("startScoreBtn").disabled = "disabled";
					if (!isNaN(numPlayers_Set) && !isNaN(numPlayers_OtherSet)) {
						if (isInteger(numPlayers_Set) && isInteger(numPlayers_OtherSet)) {
							if (Number(numPlayers_Set) > 0 && Number(numPlayers_OtherSet) > 0) {
								document.getElementById("startScoreBtn").removeAttribute("disabled");
							}
						}
					}
				}
			}
		}

		function setPlayersForGame(aGameId, idTeamA, idTeamB, status) {
			// giocatori A
			var tAplayers = document.getElementById("teamASel").selectedOptions;
			var aCodes = "" + tAplayers[0].value;
			for (var i = 1; i < tAplayers.length; i++) {
				aCodes = aCodes + ", " + tAplayers[i].value;
			}
			// giocatori B
			var tBplayers = document.getElementById("teamBSel").selectedOptions;
			var bCodes = "" + tBplayers[0].value;
			for (var i = 1; i < tBplayers.length; i++) {
				bCodes = bCodes + ", " + tBplayers[i].value;
			}
			// Send POST to "doScore" page
			var frm = document.createElement("form");
			newInput("trgr", "yess", frm);
			newInput("gameId", aGameId, frm);
			newInput("aTeamId", idTeamA, frm);
			newInput("bTeamId", idTeamB, frm);
			newInput("gameStatus", status, frm);
			newInput("aPlayers", aCodes, frm);
			newInput("bPlayers", bCodes, frm);
			frm.method = "POST";
			frm.action = "doScore.php";
			document.body.appendChild(frm);
			frm.submit();
		}

		function newInput(aName, aValue, aForm) {
			var anInpt = document.createElement("input");
			anInpt.name = aName;
			anInpt.value = aValue;
			aForm.appendChild(anInpt);
		}
	</script>
</head>
<body>
	<div>Score the game<br/><?php echo $gameName ?><span class="goBack"><a href="./home.php" id="backToManagement">Torna indietro</a></span></div>
	<p class="subtitle">Selezionare i giocatori (aggiungere eventualmente i mancanti) e confermare per procedere</p>
	<div id="teams">
		<div id="teamADiv">
			<span><?php echo $tA ?></span>
			<select id="teamASel" size="12" onChange="calcSelected(this, '_A')" multiple="multiple">
				<?php
				while ($resSet = $resTa->fetch_assoc()) {
					$desc = $resSet["numeroMaglia"] . " - " . $resSet["cognome"] . ", " . $resSet["nome"];
				?>
				<option value="<?php echo $resSet["playerId"] ?>"><?php echo $desc ?></option>
				<?php
				}
				?>
			</select>
			<div class="newPlayer">
				<input type="hidden" name="teamIdForNewPlayer_A" id="teamIdForNewPlayer_A" value="<?php echo $tAid ?>"/>
				<div class="header" id="newPlayerHeaderId_A">New player</div>
				<div># &ndash; Cognome &ndash; Nome</div>
				<div>
					<input type="text" name="newNumber_A" id="newNumber_A" maxlength="2" size="3" placeholder="#"/> &ndash;
					<input type="text" name="newSurname_A" id="newSurname_A" maxlength="50" size="20" placeholder="Player surname"/> &ndash;
					<input type="text" name="newName_A" id="newName_A" maxlength="50" size="20" placeholder="Player name"/>
					<div><input type="button" name="saveNewPlayer_A" value="Save player" onclick="saveNewPlayer('_A', 'teamASel');"/></div>
				</div>
			</div>
			<div id="confirm"><p><span>Giocatori selezionati: </span><span id="selectedPlayers_A"> - - </span></p><p><label for="selectionIsOK_A">Confermo selezione</label><input type="checkbox" id="selectionIsOK_A" onClick="enableStartScoreBtn('A', this.checked)"/></p></div>
		</div>
		<div id="teamBDiv">
			<span><?php echo $tB ?></span>
			<select id="teamBSel" size="12" onChange="calcSelected(this, '_B')" multiple="multiple">
				<?php
				while ($resSet = $resTb->fetch_assoc()) {;
				$desc = $resSet["numeroMaglia"] . " - " . $resSet["cognome"] . ", " . $resSet["nome"];
				?>
				<option value="<?php echo $resSet["playerId"] ?>"><?php echo $desc ?></option>
				<?php
				}
				?>
			</select>
			<div class="newPlayer">
				<input type="hidden" name="teamIdForNewPlayer_B" id="teamIdForNewPlayer_B" value="<?php echo $tBid ?>"/>
				<div class="header" id="newPlayerHeaderId_B">New player</div>
				<div># &ndash; Cognome &ndash; Nome</div>
				<div>
					<input type="text" name="newNumber_B" id="newNumber_B" maxlength="2" size="3" placeholder="#"/> &ndash;
					<input type="text" name="newSurname_B" id="newSurname_B" maxlength="50" size="20" placeholder="Player surname"/> &ndash;
					<input type="text" name="newName_B" id="newName_B" maxlength="50" size="20" placeholder="Player name"/>
					<div><input type="button" name="saveNewPlayer_B" value="Save player" onclick="saveNewPlayer('_B', 'teamBSel');"/></div>
				</div>
			</div>
			<div id="confirm"><p><span>Giocatori selezionati: </span><span id="selectedPlayers_B"> - - </span></p><p><label for="selectionIsOK_B">Confermo selezione</label><input type="checkbox" id="selectionIsOK_B" onClick="enableStartScoreBtn('B', this.checked)"/></p></div>
		</div>
	</div>
	<p class="tool">Se le formazioni sono corrette, premere il pulsante per procedere <input type="button" name="startScoreBtn" id="startScoreBtn" disabled="disabled" value="Avviare la rilevazione" onClick="setPlayersForGame(<?php echo $gameId . ", " . $tAid . ", " . $tBid . ", '" . $status . "'" ?>)"/><span class="goBack"><a href="./home.php" id="backToManagement">Torna indietro</a></span></p>
</body>
</html>


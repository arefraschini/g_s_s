<?php namespace gsas;
include "./include/check_session.php";
include "./conf/db.php";
include "./include/db.php";
// per il futuro leggere i settings dell'applicazione: logo, db
?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="gsas.css" />
<script type="text/javascript">
	function showAddTeam() {
		var aDiv = document.getElementById("newTeamId");
		if (aDiv != null) {
			aDiv.classList.toggle("hidden");
		}
	};

	function showAddGame() {
		var aDiv = document.getElementById("newGameId");
		if (aDiv != null) {
			aDiv.classList.toggle("hidden");
		}
	};

	function fillPlayers(aSel) {
		if (aSel == null) {
			return;
		}
		var teamId = aSel.options[aSel.selectedIndex].value;
//		alert('teamId:' + teamId);
		var teamName = aSel.options[aSel.selectedIndex].text;
		document.getElementById("teamIdForNewPlayer").value = teamId;
		document.getElementById("teamPlayersListLabel").innerHTML = "Giocatori della squadra " + teamName;
		// Read players in team
		var xmlhttp = new XMLHttpRequest();

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
				if (xmlhttp.status == 200) {
					// Read JSon data
					alert(xmlhttp.responseText);
					var txt = xmlhttp.responseText;
					var pos = txt.indexOf('}', 20);
					alert (txt.substring(pos + 1).trim());
					var jPlayers = JSON.parse(txt);
					var listText = jPlayers.length > 0 ? "" : "<option> -- Nessun giocatore trovato --</option>";;
					for (var i = 0; i < jPlayers.length; i++) {
						var tmpJp = jPlayers[i];
						listText = listText + "<option code='" + tmpJp.playerId + "'>"
									+ "#" + tmpJp.numeroMaglia + " &ndash; "
									+ tmpJp.cognome + ", " + tmpJp.nome + "</option>";
					}
					document.getElementById("playersOfTeam").innerHTML = listText;
				} else if (xmlhttp.status == 400) {
					alert('There was an error 400');
				} else {
					alert('something else other than 200 was returned');
				}
			}
		};

		xmlhttp.open("POST", "aj.php", true);
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
		xmlhttp.send("readPlayersTeamId=" + teamId);
		return;
	}

	function saveNewPlayer() {
		var teamId = document.getElementById("teamIdForNewPlayer").value;
		// Save a new player
		var xmlhttp = new XMLHttpRequest();

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
				if (xmlhttp.status == 200) {
					// Read JSon data
					var resp = JSON.parse(xmlhttp.responseText);
					var currHeader = document.getElementById("newPlayerHeaderId");
					currHeader.innerHTML = currHeader.innerHTML + " &ndash; " + resp.msg;
					currHeader.style.color = resp.isOk == "N" ? "red" : "green";
				} else if (xmlhttp.status == 400) {
					alert('There was an error 400');
				} else {
					alert('something else other than 200 was returned');
				}
			}
		};

		xmlhttp.open("POST", "aj.php", true);
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
		var newNumber = document.getElementById("newNumber").value;
		var newName = document.getElementById("newName").value;
		var newSurname = document.getElementById("newSurname").value;

		var newData = JSON.stringify({"playerTeamId": teamId, "newNumber": newNumber, "newName": newName, "newSurname": newSurname, "saveNewPlayer": "yess"});
//		let data = new FormData();
//		data.append("playerTeamId", teamId);
//		data.append("newNumber", newNumber);
//		data.append("newName", newName);
//		data.append("newSurname", newSurname);
//		data.append("saveNewPlayer", "saveNewPlayer");

//		var newData = "playerTeamId=" + teamId + "&newNumber=" + newNumber + "&newName" + newName + "&newSurname=" + newSurname + "&saveNewPlayer=yess";
		xmlhttp.send(newData);
		return;
	}
</script>
</head>
<body>
<h2>Game scorer and viewer | Home page | <?php echo session_id() ?></h2>
<?php
	echo "#<br>";
	var_dump($_SESSION);
	if (isset($_SESSION['message'])) {
	    $message = $_SESSION['message'];
	    ?>
		<h3 class="infoMsg"><?php echo $message ?></h3>
<?php
	}
	if (isset($_SESSION['errMessage'])) {
	    $errMessage = $_SESSION['errMessage'];
	    ?>
		<h3 class="errMsg"><?php echo $errMessage ?></h3>
<?php
	}
	$dbConn=getConnection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

	if (isset($_POST) && (isset($_POST["saveNewTeam"]) || isset($_POST["saveNewGame"]))) {
	    if (isset($_POST["saveNewTeam"])) {
	        $teamName = mysqli_real_escape_string($dbConn, $_POST["teamName"]);
	        $championship = mysqli_real_escape_string($dbConn, $_POST["championship"]);
	        $teamPlace = mysqli_real_escape_string($dbConn, $_POST["teamPlace"]);
	        $sqlTeam = "INSERT INTO teams (nomeTeam, campionato, citta) VALUES ('" . $teamName . "', '" . $championship . "', '" . $teamPlace . "');";
	        $dbConn->query($sqlTeam);
	    } else  if (isset($_POST["saveNewGame"])) {
	        $teamA = $_POST["teamsA"];
	        $teamB = $_POST["teamsB"];
	        $gamePlace = mysqli_real_escape_string($dbConn, $_POST["gamePlace"]);
	        $gameDetails = mysqli_real_escape_string($dbConn, $_POST["gameDetails"]);
	        $gameDate = $_POST["gameDate"];
	        $gameTime = $_POST["gameTime"];
	        $gameKey = substr(md5(rand()), 0, 7);;
	        $sqlGame = "INSERT INTO games (gameCode, teamA, teamB, gameDate, gameTime, gamePlace, gamePlaceDetails) VALUES ('"
		                  . $gameKey . "', " . $teamA . ", " . $teamB . ", '" . $gameDate . "', '" . $gameTime
	                       . "', '" . $gamePlace . "', '" . $gameDetails . "');";
	        $dbConn->query($sqlGame);
	    }
	}
?>
<p>HOME PAGE</p>
<a href="./manage_login.php?action=logout" title="Logout">Esci</a>
<h1>Game scorer and see | dati anagrafici</h1>
<div class="teamsAndPlayers">
<div class="teams">
<p>Elenco squadre registrate</p>
<?php
	$result = $dbConn->query("SELECT * FROM teams ORDER BY nomeTeam, campionato, citta");
	if ($result->num_rows > 0) {?>
		<select id="teams" name="teams" size="5" onChange="return fillPlayers(this);">
	<?php
		while ($resSet = $result->fetch_assoc()) {;
		$desc = $resSet["nomeTeam"] . " - " . $resSet["campionato"] . " (" . $resSet["citta"] . ")";
		?><option value="<?php echo $resSet["teamId"] ?>"><?php echo $desc ?></option><?php
		} ?>
		</select>
		<?php
	}
?><input type="button" value="Registra squadra" onclick="showAddTeam();"/>
<div class="newTeam hidden" id="newTeamId">
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"] ?>">
		<div class="header">Inserisci nuova squadra</div>
		<div><span>Nome squadra</span><span><input type="text" name="teamName" placeholder="Inserire il nome della nuova squadra" maxlength="50" width="30em"/></span></div>
		<div><span>Campionato/serie</span><span><input type="text" name="championship" placeholder="Campionato/serie a cui si partecipa" maxlength="50" width="30em"/></span></div>
		<div><span>Citt&agrave; della squadra</span><span><input type="text" name="teamPlace" placeholder="Inserire la citt&agrave; della nuova squadra" maxlength="50"/></span></div>
		<div><input type="submit" name="saveNewTeam" value="Salva squadra"/></div>
	</form>
</div>
</div>
<div class="players">
	<p id="teamPlayersListLabel">Players for team</p>
	<select id="playersOfTeam" name="playersOfTeam" size="10">
		<option> -- Nessuna squadra selezionata --</option>
	</select>
	<div class="newPlayer">
		<form method="POST" action="<?php echo $_SERVER["PHP_SELF"] ?>">
			<input type="hidden" name="teamIdForNewPlayer" id="teamIdForNewPlayer"/>
			<div class="header" id="newPlayerHeaderId">New player</div>
			<div># &ndash; Cognome &ndash; Nome</div>
			<div>
				<input type="text" name="newNumber" id="newNumber" maxlength="2" size="3" placeholder="#"/> &ndash;
				<input type="text" name="newSurname" id="newSurname" maxlength="50" size="20" placeholder="Player surname"/> &ndash;
				<input type="text" name="newName" id="newName" maxlength="50" size="20" placeholder="Player name"/>
				<div><input type="button" name="saveNewPlayer" value="Save player" onclick="saveNewPlayer()"/></div>
			</div>
		</form>
	</div>
</div>
</div>
<p>Elenco partite disponibili</p>
<?php
	$result = $dbConn->query("SELECT g.*, tA.nomeTeam AS tAnomeTeam, tB.nomeTeam AS tBnomeTeam, "
	. " tA.Campionato AS tAcampionato, tB.campionato AS tBcampionato, "
	. " tA.citta AS tAcitta, tB.citta AS tBcitta FROM games AS g "
	. "INNER JOIN teams AS tA ON g.teamA = tA.teamId "
	. "INNER JOIN teams AS tB ON g.teamB = tB.teamId "
	. "WHERE g.gameDate >= CURDATE()");
	if ($result->num_rows > 0) {?>
		<select id="games" name="games" size="10">
		<?php
		while ($resSet = $result->fetch_assoc()) {;
		$descTa = $resSet["tAnomeTeam"] . " - " . $resSet["tAcampionato"] . " (" . $resSet["tAcitta"] . ")";
		$descTb = $resSet["tBnomeTeam"] . " - " . $resSet["tBcampionato"] . " (" . $resSet["tBcitta"] . ")";
		$desc = $descTa . " vs " . $descTb . " @" . $resSet["gamePlace"] . " | " . $resSet["gameDate"];
	       ?><option value="<?php echo $resSet["gameId"] ?>"><?php echo $desc ?></option><?php
		}
		?>
		</select><?php
	}
?><input type="button" value="Aggiungi partita" onclick="showAddGame();"/>
<div class="newGame hidden" id="newGameId">
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] ?>">
    	<div class="header">Inserisci nuova partita</div>
    	<div class="team teamA">
    		<span>Squadra A</span>
    		<span>
        	<?php
        	$result = $dbConn->query("SELECT * FROM teams ORDER BY nomeTeam, campionato, citta");
        	if ($result->num_rows > 0) {?>
        		<select id="teamsA" name="teamsA" size="5">
        	<?php
        		while ($resSet = $result->fetch_assoc()) {;
        		$desc = $resSet["nomeTeam"] . " - " . $resSet["campionato"] . " (" . $resSet["citta"] . ")";
        		?><option value="<?php echo $resSet["teamId"] ?>"><?php echo $desc ?></option><?php
        		} ?>
        		</select>
        	<?php } ?>
    		</span>
    	</div>
    	<div class="team teamB">
    		<span>Squadra B</span>
    		<span>
        	<?php
        	// Mi rimetto sul primo elemento per rileggere i dati
        	$result->data_seek(0);
        	if ($result->num_rows > 0) {?>
        		<select id="teamsB" name="teamsB" size="5">
        	<?php
        		while ($resSet = $result->fetch_assoc()) {;
        		$desc = $resSet["nomeTeam"] . " - " . $resSet["campionato"] . " (" . $resSet["citta"] . ")";
        		?><option value="<?php echo $resSet["teamId"] ?>"><?php echo $desc ?></option><?php
        		} ?>
        		</select>
        	<?php }
        	$result->free_result();
        	$dbConn->close();
	       ?>
    		</span>
    	</div>
    	<div><span>Localit&agrave;</span><span><input type="text" name="gamePlace" placeholder="Localit&agrave; dell'incontro" maxlength="100"/></span></div>
    	<div><span>Dettagli (palestra, indirizzo, ...)</span><span><input type="text" name="gameDetails" placeholder="Dettagli dell'incontro" maxlength="100"/></span></div>
    	<div><span>dt</span><span><input type="date" name="gameDate" placeholder="gg/mm/aaaa" maxlength="50"/></span></div>
    	<div><span>hr</span><span><input type="time" name="gameTime" placeholder="hh:MM" maxlength="50"/></span></div>
    	<div><input type="submit" name="saveNewGame" value="Salva partita"/></div>
    </form>
</div>
</body>
</html>
<?php include "./include/footer.php" ?>

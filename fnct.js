function showDetails(plId, gameId, teamId) {
	document.getElementById("detailsOverlay").style.display = "block";
	// Team name
	document.getElementById("detPLayerTeam").innerHTML = document.getElementById("team_" + teamId).innerHTML;
	// Player name
	var li = document.getElementById("_" + plId);
	var name = document.getElementById("detPlName");
	name.innerHTML = li.innerHTML;
	// Ajax call
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
			if (xmlhttp.status == 200) {
				// Read JSon data
				var txt = xmlhttp.responseText;
				// Read data and set values
				var jPlayer = JSON.parse(txt);
				if (jPlayer.msg == "OK") {
					document.getElementById("hiddenPlayerIdDetails").value = jPlayer.playerId;
					document.getElementById("fouls").innerHTML = jPlayer.fouls;
					document.getElementById("tl").innerHTML = jPlayer.tl;
					document.getElementById("tl_ok").innerHTML = jPlayer.tl_ok;
					document.getElementById("p2").innerHTML = jPlayer.p2;
					document.getElementById("p2_ok").innerHTML = jPlayer.p2_ok;
					document.getElementById("p3").innerHTML = jPlayer.p3;
					document.getElementById("p3_ok").innerHTML = jPlayer.p3_ok;
					document.getElementById("rimbA").innerHTML = jPlayer.rimbA;
					document.getElementById("rimbD").innerHTML = jPlayer.rimbD;
					document.getElementById("palPer").innerHTML = jPlayer.palPer;
					document.getElementById("palRec").innerHTML = jPlayer.palRec;
					document.getElementById("detPlTot").innerHTML = jPlayer.points;
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
	xmlhttp.send(JSON.stringify({"gamePlayerDetails": "ohYess", "gameId": gameId, "playerId": plId}));
	return;
}

function hideThis(anElement) {
	//document.getElementById("detailsOverlay").style.display = 'none';
	anElement.parentNode.parentNode.parentNode.style.display = 'none';
}

function showBtns(anElement, plId, gameId) {
	hideShowBtns(anElement, plId, gameId, true);
}

function hideBtns(anElement, plId, gameId) {
	hideShowBtns(anElement, plId, gameId, false);
}

function hideShowBtns(anElement, plId, gameId, setVisible) {
	var listItem = anElement.parentNode.parentNode.parentNode;
	var itemHolder = listItem.parentNode;
	// 0. Send request to the server

	// Ajax call
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
			if (xmlhttp.status == 200) {
				// Read JSon data
				var txt = xmlhttp.responseText;
				// Read data and set values
				var jPlayer = JSON.parse(txt);
				if (jPlayer.msg == "OK") {
					// Nothing to do
				} else {
					alert(jPlayer.msg);
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
	xmlhttp.send(JSON.stringify({"setPlayerAsOnTheField": (setVisible ? "1" : "0"), "gameId": gameId, "playerId": plId}));

	// 1. Hide or show actions buttons
	var btns = listItem.getElementsByClassName("actions");
	for (var i = 0; i < btns.length; i++) {
		btns[i].style.display = setVisible ? "block" : "none";
	}
	// 2. Remove current 'listItem' from items of the UL and...
	itemHolder.removeChild(listItem);
	if (!setVisible) {
		// 2a. ...add it as last
		itemHolder.appendChild(listItem);
	} else {
		// 2b. ...move the current item to the first position in the UL list
		itemHolder.insertBefore(listItem, itemHolder.firstChild);
	}
	// 3. Switch visibility for IN/OUT buttons
	anElement.style.display = "none";
	document.getElementById(anElement.id == ("in_" + plId) ? "out_" + plId : "in_" + plId).style.display = "block";
}

function setOkPts(plId, gameId, points, team, idTeam) {
	setPts(plId, gameId, points, true, team, idTeam);
}

function setKoPts(plId, gameId, points, team, idTeam) {
	setPts(plId, gameId, points, false, team, idTeam);
}

function setPts(plId, gameId, points, isOk, team, idTeam) {
	ptsSpan = document.getElementById("pts_" + plId);
	// Ajax call
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
			if (xmlhttp.status == 200) {
				// Read JSon data
				var txt = xmlhttp.responseText;
				// Read data and set values
				var jPlayer = JSON.parse(txt);
				if (jPlayer.msg == "OK") {
					ptsSpan.innerHTML = jPlayer.points;
					teamLetter = jPlayer.teamLetter;
					document.getElementById("team" + teamLetter).innerHTML = jPlayer.teamPoints;
				}
			} else if (xmlhttp.status == 400) {
				alert('There was an error 400');
			} else {
				alert('something else other than 200 was returned');
			}
		}
	};
	dbField = ""
	switch (points) {
		case 1:
			dbField = "tl";
			break;
		case 2:
			dbField = "p2";
			break;
		case 3:
			dbField = "p3";
			break;
		default:
	}

	xmlhttp.open("POST", "aj.php", true);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	xmlhttp.send(JSON.stringify({"pointsForPlayer": "ohYess", "gameId": gameId, "playerId": plId, "teamLetter": team, "idTeam": idTeam, "dbField": dbField, "made": (isOk ? 1 : 0)}));
	return;
}

function setRBAtt(plId, gameId, spanId, team, idTeam) {
	setStatsData(plId, gameId, spanId, spanId, team, idTeam);
}

function setRBDif(plId, gameId, spanId, team, idTeam) {
	setStatsData(plId, gameId, spanId, spanId, team, idTeam);
}

function setPRec(plId, gameId, spanId, team, idTeam) {
	setStatsData(plId, gameId, spanId, spanId, team, idTeam);
}

function setPPer(plId, gameId, spanId, team, idTeam) {
	setStatsData(plId, gameId, spanId, spanId, team, idTeam);
}

function setStatsData(plId, gameId, spanId, dbField, team, idTeam) {
	spanData = document.getElementById(spanId);
	// Ajax call
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
			if (xmlhttp.status == 200) {
				// Read JSon data
				var txt = xmlhttp.responseText;
				// Read data and set values
				var jPlayer = JSON.parse(txt);
				if (jPlayer.msg == "OK") {
					spanData.innerHTML = jPlayer.data;
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
	xmlhttp.send(JSON.stringify({"dataForPlayer": "ohYess", "gameId": gameId, "playerId": plId, "teamLetter": team, "idTeam": idTeam, "dbField": dbField}));
	return;
}

function cleanQuarterClass() {
	document.getElementById('q1').classList.remove("active");
	document.getElementById('q2').classList.remove("active");
	document.getElementById('q3').classList.remove("active");
	document.getElementById('q4').classList.remove("active");
	document.getElementById('ot1').classList.remove("active");
	document.getElementById('ot2').classList.remove("active");
}

function setQuarter(anId, gameId) {
	spanData = document.getElementById(anId);
	// Ajax call
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
			if (xmlhttp.status == 200) {
				// Read JSon data
				var txt = xmlhttp.responseText;
				// Read data and set values
				var jPlayer = JSON.parse(txt);
				if (jPlayer.msg == "OK") {
					cleanQuarterClass();
					spanData.classList.add("active");
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
	xmlhttp.send(JSON.stringify({"quarterOfGame": "ohYess", "gameId": gameId, "quarter": anId}));
	return;
}

function showHideFoulsPad() {
	var aDiv = document.getElementById("foulsPad");
	if (aDiv != null) {
		var aVisibilityStyle = aDiv.style.display;
		aDiv.style.display = aVisibilityStyle == "none" ? "block" : "none";
	}
}

function append(gameId, foulType) {
	workWithFouls(gameId, foulType, "APPEND");
}

function removeLast(gameId, foulType) {
	workWithFouls(gameId, foulType, "REM_LAST");
}

function workWithFouls(gameId, foulType, opType) {
	spanData = document.getElementById("fouls");
	var registeredFouls = spanData.innerHTML;
	var plId = document.getElementById("hiddenPlayerIdDetails").value;
	// Ajax call
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
			if (xmlhttp.status == 200) {
				// Read JSon data
				var txt = xmlhttp.responseText;
				// Read data and set values
				var jPlayer = JSON.parse(txt);
				if (jPlayer.msg == "OK") {
					spanData.innerHTML = jPlayer.fouls;
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
	xmlhttp.send(JSON.stringify({"personalFouls": "ohYess", "gameId": gameId, "playerId": plId, "foulType": foulType, "operation": opType, "regFouls": registeredFouls}));
	return;
}
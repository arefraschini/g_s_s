<?php

?>
<!DOCTYPE html>
<html>
	<head>
		<title>The live game... by numbers</title>
		<link type="text/css" rel="stylesheet" href="../gsas.css" />
		<script type="text/javascript">
			function hideAndShow(prefix1, prefix2) {
				// 1-hide | 2-show
				var label1 = document.getElementById(prefix1 + "TeamName");
				var label2 = document.getElementById(prefix2 + "TeamName");
				var div1 = document.getElementById(prefix1 + "TeamData");
				var div2 = document.getElementById(prefix2 + "TeamData");
				label1.style.opacity = 0.5;
				label2.style.opacity = 1;
				div1.style.display = "none";
				div2.style.display = "block";
			}
		</script>
	</head>
	<body>
		<h1 id="gameTitle">Nome partita</h1>
		<div class="currentScore"><span id="homeScore">xx</span>&ndash;<span id="guestScore">yy</span></div>
		<div class="teamLabels"><span id="homeTeamName" onclick="hideAndShow('guest', 'home');">home team</span><span id="guestTeamName" onclick="hideAndShow('home', 'guest');">guest team</span></div>
		<div class="dataBox homeData" id="homeTeamData"></div>
		<div class="dataBox guestData" id="guestTeamData"></div>
	</body>
</html>
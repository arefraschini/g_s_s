<?php namespace gsas;
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// per il futuro leggere i settings dell'applicazione: logo, db
?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="gsas.css" />
</head>
<body>
<h2>Game scorer and viewer | <?php echo session_id() ?></h2>
<?php
    echo "<p>pippo1</p>";
    print_r($_REQUEST);
if (isset($_REQUEST['errMsg'])) {
    echo "<p>pippo2</p>";
    $errMsg = $_REQUEST['errMsg'];
    ?>
    <h3 class="errMsg"><?php echo $errMsg ?></h3>
    <?php
}
?>
<form method="post" action="main.php">
    <div class="login">
    	<div class="hldr"><span>utente</span><input type="text" name="myName"/></div>
    	<div class="hldr"><span>password</span><input type="password" name="myPwd" autocomplete="off"/></div>
    	<div class="hldr"><span><input type="submit" name="loginBtn" value="Login"/></span></div>
    </div>
</form>
</body>
</html>
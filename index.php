<?php namespace gsas;
session_start();
// per il futuro leggere i settings dell'applicazione: logo, db
?>
<html>
<head>
<link type="text/css" rel="stylesheet" href="gsas.css" />
</head>
<body>
<h2>Game scorer and viewer | <?php echo "abc123" . session_id() ?></h2>
<?php
var_dump($_SESSION);

// clear session messages
function clearMessages(){
    unset($_SESSION["errMessage"]);
    unset($_SESSION["message"]);
    // TODO : add here other variables to unset
}

if (isset($_SESSION['message']) || isset($_SESSION['errMessage'])) {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
    ?>
    <h3 class="infoMsg"><?php echo $message ?></h3>
<?php
        }
    }

    if (isset($_SESSION['errMessage'])) {
        $message = $_SESSION['errMessage'];
    ?>
    <h3 class="errMsg"><?php echo $message ?></h3>
<?php
    }
?>
<form method="post" action="manage_login.php">
    <div class="login">
        <input type="hidden" name="action" value="login" />
    	<div class="hldr"><span>utente</span><input type="text" name="myName"/></div>
    	<div class="hldr"><span>password</span><input type="password" name="myPwd" autocomplete="off"/></div>
    	<div class="hldr"><span><input type="submit" name="loginBtn" value="Login"/></span></div>
    </div>
</form>
</body>
</html>
<?php include "./include/footer.php" ?>

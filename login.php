<?php session_start();if(isset($_SESSION["login"]))header('Location: index.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!--<link rel="stylesheet" type="text/css" href="scripts/jquery-ui/jquery-ui.css">-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="./styles/styles.css">
	<!--<script src="https://code.jquery.com/jquery-1.10.2.js"></script>-->
	<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui/jquery-ui.js"></script>
	<title>Log in to GaliDAV</title>
</head>
<body>
	<div id="logbox">
		<h2 type="log-welcome"> Accueil GaliDAV </h2>
		<form method="post" action="loginCheck.php" enctype="multipart/form-data">
			<fieldset><legend> Identifiants </legend>
				<label for="username"> Nom d'utilisateur :</label>  <input name="username" type="text" id="username" required autofocus /><br /><br />
				<label for="password"> Mot de passe:  </label><input type="password" name="password" id="password" required /><br />
			</fieldset>

			<p><input type="submit" value="Connect" /></p>
		</form>
	</div>
</body>
</html>

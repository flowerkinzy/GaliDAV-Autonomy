<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("classes/C_Database.php");
require_once("functions/queries.php");

$error=''; // Msg d'erreur

/*if (empty($_POST['username']) || empty($_POST['password'])) {
	$error = "Username or Password is invalid";
	var_dump($error);
	die();
}
else
{*/
	$login=$_POST['username'];
	$password=$_POST['password'];
	$login = stripslashes($login);
	$password = stripslashes($password);
	$login = pg_escape_string($login);

	$dbPassword = Database::currentDB()->executeQuery(query_login($login));
	
	if(pg_num_rows($dbPassword) == 1)
	{
		if(password_verify($password, pg_fetch_result($dbPassword, "password")))
		{
			$_SESSION['login']=$login;
			header("location: index.php");
		}
	}
	
	header("location: login.php");
	$error = "Username or Password is invalid";
//}

?>
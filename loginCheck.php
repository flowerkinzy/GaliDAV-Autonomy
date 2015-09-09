<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("classes/C_Person.php");
require_once("classes/C_Database.php");
require_once("functions/queries.php");
$error = "Username or Password is invalid";
if (empty($_POST['username']) || empty($_POST['password'])) {
	header("Location: login.php");
}
else
{
	$login=$_POST['username'];
	$password=$_POST['password'];
	$login = stripslashes($login);
	$password = stripslashes($password);
	$login = pg_escape_string($login);
	$dbPassword = Database::currentDB()->executeQuery(query_user_from_login($login));
	if(pg_num_rows($dbPassword) == 1)
	{
		$ressource=pg_fetch_assoc($dbPassword);
		if(password_verify($password, $ressource["password"]))
		{
			session_destroy();
			session_start();
			$_SESSION = array();
			$_SESSION['login']=$login;
			$P=new Person();
			$P->loadFromDB(intval($ressource["id_person"]));
			$_SESSION['user_family_name']=$P->getFamilyName();
			$_SESSION['user_first_name']=$P->getFirstName();
			header("Location: index.php");
		}
		else{
			header("Location: login.php");
		}
	}
	else{
		header("Location: login.php");
	}
	
}
?>
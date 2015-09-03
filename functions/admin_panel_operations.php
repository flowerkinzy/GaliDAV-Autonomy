<?php
/**
 * \file  test_davical_operations.php
 * \brief Contains all the operations on DAViCal’s database.
*/
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once("functions/error_handling.php");

require_once("classes/C_Subject.php");
require_once("classes/C_Class.php");
require_once("classes/C_Administrator.php");
require_once("classes/C_Teacher.php");
require_once("classes/C_Secretary.php");

require_once("config/path.php");



//require_once("classes/C_Head.php");




if (isset($_POST['action']))
{
	if ($_POST['action'] == 'add_subject')
	{

		$aGroup = new Group();
		
		if ($aGroup->loadFromDB(intval($_POST['groupname'])))
		{
			//var_dump($_POST['subjectname']);
			//die( print_r($_POST['subjectname']));
			$aSubject = new Subject(strval($_POST['subjectname']),intval($aGroup->getSqlId()));
			//die("groupe trouvé");
			//die("group trouvé (id="+$_POST['groupname']+ "+ matière créée");
			if ($_POST['speaker1'] != "--")
			{
				$res = Database::currentDB()->executeQuery(query_person_by_fullname($_POST['speaker1']));

				if($res)
				{
					$result = pg_fetch_assoc($res);
					$aPerson = new Person();
					//$aPerson->loadFromDB($result['id']);
					$aSubject->addTeacher(intval($result['id']));
				}
				else
				{
					Database::currentDB()->showError();
				}
			}

			if ($_POST['speaker2'] != "--")
			{
				$res = Database::currentDB()->executeQuery(query_person_by_fullname($_POST['speaker2']));

				if ($res)
				{
					$result = pg_fetch_assoc($res);
					//$aPerson = new Person();
					//$aPerson->loadFromDB($result['id']);
					$aSubject->addTeacher(intval($result['id']));
				}
				else
				{
					Database::currentDB()->showError();
				}
			}

			if ($_POST['speaker3'] != "--")
			{
				$res = Database::currentDB()->executeQuery(query_person_by_fullname($_POST['speaker3']));

				if ($res)
				{
					$result = pg_fetch_assoc($res);
					//$aPerson = new Person();
					//$aPerson->loadFromDB($result['id']);
					$subject->addTeacher(intval($result['id']));
				}
				else
				{
					Database::currentDB()->showError();
				}
			}
		}else die("group non retrouvé (id="+$_POST['groupname']);

		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
		die;
	}

	if ($_POST['action'] == 'add_user')
	{
		if ($_POST['password'] != $_POST['password2'])
		{
			header('Location: '.GALIDAV_PATH.'/admin_panel2.php?GMESSAGE_ERROR=DIFFERENT_PASS');
			die;
		}
		else
		{
			if ($_POST['status'] == 'secretary')
			{
				$aUser = new Secretary($_POST['familyname'], $_POST['firstname'], $_POST['login'], $_POST['password'], $_POST['email']);
			}
			else if ($_POST['status'] == 'teacher')
			{
				$aUser = new Teacher($_POST['familyname'], $_POST['firstname'], $_POST['login'], $_POST['password'], $_POST['email']);
			}
			else if ($_POST['status'] == 'head')
			{
				$aUser = new Head($_POST['familyname'], $_POST['firstname'], $_POST['login'], $_POST['password'], $_POST['email']);
			}
			else if ($_POST['status'] == 'administrator')
			{
				$aUser = new Administrator($_POST['familyname'], $_POST['firstname'], $_POST['login'], $_POST['password'], $_POST['email']);
			}

			echo($aUser->toHTML());
			header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
			die;
		}
	}

	if ($_POST['action'] == 'add_group')
	{
		if( $_POST['isaclass']==true)$aGroup = new C_Class($_POST['name']);
		else $aGroup = new Group($_POST['name'],false);
		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
	}

	if ($_POST['action'] == 'add_person')
	{
		$aPerson = new Person($_POST['familyname'], $_POST['firstname'], $_POST['email']);

		if ($_POST['status'] == 'student')
		{
			$aPerson->addStatus(new PersonStatus(PersonStatus::STUDENT));
		}
		else if ($_POST['status'] == 'speaker')
		{
			$aPerson->addStatus(new PersonStatus(PersonStatus::SPEAKER));
		}

		echo($aPerson->toHTML());
		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
		die;
	}

	if ($_POST['action'] == 'delete_person')
	{
		$aPerson = new Person();
		$aPerson->loadFromDB(intval($_POST['id']));
		$aPerson->removeFromDB();
		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
		die;
	}

	if ($_POST['action'] == 'clear_db')
	{
		Database::currentDB()->clear();
		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
		die;
	}

	if ($_POST['action'] == 'init_db')
	{
		Database::currentDB()->initialize();
		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
		die;
	}

	if ($_POST['action'] == 'delete_group')
	{
		$aGroup = new Group();

		if (!$aGroup->loadFromDB(intval($_POST['id'])))
		{
			die('Group not found');
		}
		else
		{
			echo("G id/name= " . $aGroup->getSqlId() . " / " . $aGroup->getName());
		}

		$aGroup->removeFromDB();
		header('Location: '.GALIDAV_PATH.'/admin_panel2.php');
		die;
	}

	if ($_POST['action'] == 'modify_group')
	{
		$aGroup = new Group();

		if (!$aGroup->loadFromDB(intval($_POST['id'])))
		{
			die('Error: Group' . $_POST['id'] . 'not found.');
		}
		else
		{
			echo("G id/name = " . $aGroup->getSqlId() . " / " . $aGroup->getName());
		}
	}
}
?>

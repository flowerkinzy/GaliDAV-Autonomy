<?php
/**
 * \file    groups_functions.php
 * \brief   Contains functions which allow to manage groups.
*/
error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5'))
{
	die('This file was written for PHP 5');
}

include_once("functions/error_handling.php");

require_once("classes/C_Group.php")
require_once("classes/C_Timetable.php")

function create_new_group($name, $isAClass = FALSE, $studentsList = array(), $linkedGroupsList = array())
{
	$group = new Group($name, $isAClass);
	$sql_error = Database::currentDB()->sqlErrorMessage;

	if ($sql_error != "")
	{
		return "";
	}

	if ($group->getSqlId() == NULL)
	{
		return "";
	}

	if (is_array($studentList))
	{
		$group->setStudentsList($studentList)
	}

	$sql_error = Database::currentDB()->sqlErrorMessage;

	if ($sql_error != "")
	{
		return "";
	}

	if (is_array($linkedGroupsList))
	{
		$group->setLinkedGroupsList($linkedGroupsList)
	}

	$sql_error = Database::currentDB()->sqlErrorMessage;

	if ($sql_error != "")
	{
		return "";
	}

	return json_encode($group->to_array());
}
?>

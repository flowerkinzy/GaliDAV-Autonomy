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

require_once("classes/C_Group.php");
require_once("classes/C_Timetable.php");
require_once("classes/C_Course.php");

//Flora NOTE: since group is created by admin dans admin_panel, and it is probable that group linking would be made there,
// it is better to focus on student list of a group
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
		$group->setStudentsList($studentList);
	}

	$sql_error = Database::currentDB()->sqlErrorMessage;

	if ($sql_error != "")
	{
		return "";
	}

	if (is_array($linkedGroupsList))
	{
		$group->setLinkedGroupsList($linkedGroupsList);
	}

	$sql_error = Database::currentDB()->sqlErrorMessage;

	if ($sql_error != "")
	{
		return "";
	}

	return json_encode($group->to_array());
}


function get_timetable_groups_between($id_calendar, $begin, $end)
{
	$result = array();

	if (is_int($id_calendar) && is_int($begin) && is_int($end))
	{
		$timetable = new Timetable();

		$timetable->loadFromDB($id_calendar);

		if (is_int($timetable->getSqlId()))
		{
			$coursesList = $timetable->getCoursesListBetween($begin, $end);

			foreach ($coursesList as $oneCourseId)
			{
				$course = new Course();

				$course->loadFromDB($oneCourseId);

				if (is_int($course->getSqlId()))
				{
					$result[] = json_encode($course->to_array());
				}
			}
		}

		$result = json_encode($result);
	}

	return $result;
}
?>

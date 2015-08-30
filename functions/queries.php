<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("classes/C_Database.php");



function query_all_people()
{
	return "SELECT * FROM " . Person::TABLENAME . " ORDER BY familyName;";
}

function query_all_people_names()
{
	return "SELECT id, familyname, firstname FROM " . Person::TABLENAME . ";";
}

function query_all_students()
{
	return "SELECT * FROM " . Person::TABLENAME . " AS P WHERE EXISTS(SELECT * FROM " . PersonStatus::TABLENAME . " AS S WHERE S.id_person = P.id AND S.status = 1) ORDER BY familyName;";
}

function query_all_teachers()
{
	return "SELECT * FROM " . Person::TABLENAME . " AS P WHERE EXISTS(SELECT * FROM " . PersonStatus::TABLENAME . " AS S WHERE S.id_person = P.id AND S.status = 3) ORDER BY familyName;";
}

function query_all_speakers()
{
	return "SELECT * FROM " . Person::TABLENAME . " AS P WHERE EXISTS(SELECT * FROM " . PersonStatus::TABLENAME . " AS S WHERE S.id_person = P.id AND S.status IN (2,3)) ORDER BY familyName;";
}

function query_all_groups()
{
	return "SELECT * FROM " . Group::TABLENAME . " ORDER BY name;";
}

function query_one_group($idOrName)
{
	if (is_string($idOrName))
	{
		return "SELECT * FROM " . Group::TABLENAME . " WHERE NAME = '" . pg_escape_string($idOrName) . "';";
	}
	else if (is_int($idOrName))
	{
		return "SELECT * FROM " . Group::TABLENAME . " WHERE id = $idOrName;";
	}
}

function query_all_subjects($idGroup)
{
	return "SELECT * FROM " . Subject::TABLENAME . " WHERE id_group=".$idGroup." ORDER BY name;";
}

function query_one_subject($idOrName)
{
	if (is_string($idOrName))
	{
		return "SELECT * FROM " . Suject::TABLENAME . " WHERE NAME = '" . pg_escape_string($idOrName) . "';";
	}
	else if (is_int($idOrName))
	{
		return "SELECT * FROM " . Suject::TABLENAME . " WHERE id = $idOrName;";
	}
}

function query_person_by_fullname($fullname)
{
	return "SELECT * FROM " . Person::TABLENAME . " WHERE familyname || ' ' || firstname = '" . pg_escape_string($fullname) . "';";
}
?>
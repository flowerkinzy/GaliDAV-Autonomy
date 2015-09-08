<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("KFK - Has loaded ".__FILE__);

require_once("classes/C_Database.php");
require_once("functions/queries.php");
if(isset($_GET['action'])){
	if($_GET['action']=="get_lists_subjects" && isset($_GET["id_group"])){
		if(isset($_GET["id_selected"]))echo XoptionSubjects($_GET["id_group"],intval($_GET["id_selected"]));
		else echo XoptionSubjects($_GET["id_group"]);
		//echo "<option>1<option>2";
	}
	if($_GET['action']=="get_list_classes"){
		echo get_all_classes();
		//echo "<option>1<option>2";
	}
	if($_GET['action']=="get_linked_groups" && isset($_GET["id"])){
		echo get_linked_groups(intval($_GET["id"]));
		//echo "<option>1<option>2";
	}
	if($_GET['action']=="get_list_classes_with_linked_groups"){
		echo get_all_classes_with_linked_groups();
		//echo "<option>1<option>2";
	}



}

function XListAll()
{
	$out = "<ul class = listOfPeople style = 'overflow-y:auto;height:80%;'>";
	$res = Database::currentDB()->executeQuery(query_all_people_names());

	if ($res)
	{
		$person = pg_fetch_assoc($res);

		while ($person)
		{
			// $out .= "<li>" . $person['familyname'] . " " . $person['firstname'] . "</li>";
			$out .= "<li>" . XPerson($person) . "</li>";
			$person = pg_fetch_assoc($res);
		}
	}
	else
	{
		Database::currentDB()->showError();
	}

	$out .= "</ul>";

	return $out;
}

function XListStudents()
{
	$out = "<ul class = listOfPeople style = 'overflow-y:auto;height:80%;'>";
	$res = Database::currentDB()->executeQuery(query_all_students());

	if ($res)
	{
		$person = pg_fetch_assoc($res);

		while ($person)
		{
			// $out .= "<li>" . $person['familyname'] . " " . $person['firstname'] . "</li>";
			$out .= "<li>" . XPerson($person) . "</li>";
			$person = pg_fetch_assoc($res);
		}
	}
	else
	{
		Database::currentDB()->showError();
	}

	$out .= "</ul>";

	return $out;
}

function XListTeachers()
{
	$out = "<ul class = listOfPeople style = 'overflow-y:auto;height:80%;'>";
	$res = Database::currentDB()->executeQuery(query_all_teachers());

	if($res)
	{
		$person = pg_fetch_assoc($res);

		while ($person)
		{
			// $out .= "<li>" . $person['familyname'] . " " . $person['firstname'] . "</li>";
			$out .= "<li>" . XPerson($person) . "</li>";
			$person = pg_fetch_assoc($res);
		}
	}
	else
	{
		Database::currentDB()->showError();
	}

	$out .= "</ul>";

	return $out;
}

function XListAllGroups()
{
	$out = "<ul class = listOfGroup style = 'overflow-y:auto;height:80%;'>";
	$res = Database::currentDB()->executeQuery(query_all_groups());

	if($res)
	{
		$group = pg_fetch_assoc($res);

		while ($group)
		{
			// $out .= "<li>" . $person['familyname'] . " " . $person['firstname'] . "</li>";
			$out .= "<li>" . XGroup($group) . "</li>";
			$group = pg_fetch_assoc($res);
		}
	}
	else
	{
		Database::currentDB()->showError();
	}

	$out .= "</ul>";

	return $out;
}

function XoptionSpeakers()
{
	// $out = "<datalist class = optionOfPeople id = listspeakers'>";
	$out = "<option>--";
	$res = Database::currentDB()->executeQuery(query_all_speakers());
	$person = pg_fetch_assoc($res);

	while ($person != NULL)
	{
		// $out .= "<option value='" . $person['familyname'] . " " . $person['firstname'] . "'>";
		$out .= "<option>" . $person['familyname'] . " " . $person['firstname'];
		$person = pg_fetch_assoc($res);
	}

	// $out .= "</datalist>";

	return $out;
}

function XoptionGroups()
{
	// $out = "<datalist class = optionOfGroup id = listgroups'>";
	$out = "";
	$res = Database::currentDB()->executeQuery(query_all_groups());
	$group = pg_fetch_assoc($res);

	while ($group != NULL)
	{
		// $out .= "<option value='" . $person['familyname'] . " " . $person['firstname'] . "'>";
		$out .= "<option value=".$group['id']." >" . $group['name'];
		$group = pg_fetch_assoc($res);
	}

	//$out .= "</datalist>";

	return $out;
}

function XoptionSubjects($idGroup,$idSelected=NULL)
{
	// $out = "<datalist class = optionOfGroup id = listsubjects'>";
	$out = "";
	$res = Database::currentDB()->executeQuery(query_all_subjects($idGroup));
	$subject = pg_fetch_assoc($res);

	while ($subject != NULL)
	{
		// $out .= "<option value='" . $person['familyname'] . " " . $person['firstname'] . "'>";
		if(is_int($idSelected) && $subject['id']==$idSelected) $out .= "<option value=".$subject['id']." selected >" . $subject['name'];
		else $out .= "<option value=".$subject['id']." >" . $subject['name'];
		$subject = pg_fetch_assoc($res);
	}

	//$out .= "</datalist>";

	return $out;
}

function XPerson($ressource)
{
	if (is_array($ressource))
	{
		$out = "";
		$out .= "<form action = 'functions/admin_panel_operations.php' method = 'POST'>" . $ressource['familyname'] . " " . $ressource['firstname'];
		$out .= "<input type = 'hidden' name = 'action' value = 'delete_person' /><input type = 'hidden' name = 'id' value = " . $ressource['id'] . " /><input type = 'submit' value = 'Supprimer' /></form>";

		return $out;	
	}
}

function XGroup($ressource)
{
	if (is_array($ressource))
	{
		$out = "";
		$out .= "<form action = 'functions/admin_panel_operations.php' method = 'POST'>" . $ressource['name'];
		$out .= "<input type = 'hidden' name = 'action' value = 'delete_group' /><input type = 'hidden' name = 'id' value = " . $ressource['id'] . " /><input type = 'submit' value = 'Supprimer' /></form>";

		return $out;
	}
}

function XSubject($ressource)
{
	if (is_array($ressource))
	{
		$out = "";
		$out .= "<form method = 'POST'>" . $ressource['name'];
		$out .= "<input type = 'hidden' name = 'action' value = 'delete_subject' /><input type = 'hidden' name = 'id' value = " . $ressource['id'] . " /><input type = 'submit' value = 'Supprimer' /></form>";

		return $out;	
	}
}

function get_all_classes(){
	
}

function get_all_classes_with_linked_groups(){
	$res = Database::currentDB()->executeQuery(query_all_classes());
	$class = pg_fetch_assoc($res);
	$result=array();
	while ($class!= NULL)
	{
		$obj=array();
		$obj["id"]=intval($class['id']);
		$obj["id_timetable"]=intval($class['id_current_timetable']);
		$obj["name"]=$class['name'];
		$obj["linked_groups"]=get_linked_groups(intval($class['id']));
		$result[]=json_encode($obj);
		$class = pg_fetch_assoc($res);
	}
	if(count($result)==0)return "";
	return json_encode($result);
}

function get_linked_groups($idgroup){
	$res = Database::currentDB()->executeQuery(query_all_linked_groups($idgroup));
	$class = pg_fetch_assoc($res);
	$result=array();
	while ($class!= NULL)
	{
		$obj=array();
		$obj["id"]=intval($class['id']);
		$obj["id_timetable"]=intval($class['id_current_timetable']);
		$obj["name"]=$class['name'];
		$result[]=json_encode($obj);
		$class = pg_fetch_assoc($res);
	}
	return json_encode($result);
}

?>

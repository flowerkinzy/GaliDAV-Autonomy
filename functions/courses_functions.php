<?php
//require_once("classes/C_Course.php");
//require_once("classes/C_Subject.php");

error_log("KFK - Has loaded ".__FILE__);
echo("<p>OK</p>");
// if(isset($_POST)){
// 	echo("OK");
// }

// if(isset($_POST['action'])){
// 	echo("A");
// 	if($_POST['action']=='create_course'){
// 		$result=create_new_course($_POST['begin'],$_POST['end'],$_POST['room'],$_POST['id_subject'],$_POST['type']);
// 		if(is_array($result))echo("ERREUR");
// 		else echo("SUCCESS?");
// 	}
// }

// if(isset($_GET['action'])){
// 
// // 	if($_GET['action']=='create_course'){
// // 		echo create_new_course($_GET['begin'],$_GET['end'],$_GET['room'],$_GET['id_subject'],$_GET['type']);
// // 	}
// }

function create_new_course($begin, $end,$room=null,$id_subject=NULL,$type){

	if(is_int($id_subject)){
		$S=new Subject();
		$S->loadFromDB($id_subject);
		$C = new Course($S,$begin,$end);
	}else $C = new Course(null,$begin,$end);
	$C->setCourseType($id_subject);
	if(is_string($room))$C->setRoom($room);
	if(Database::currentDB()->sqlErrorMessage!="")return new array("SQL_ERROR"=>Database::currentDB()->showError());
	return addslashes(json_encode($C->to_array()));
}


?>
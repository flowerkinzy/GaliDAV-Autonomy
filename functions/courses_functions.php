<?php
require_once("classes/C_Course.php");
require_once("classes/C_Subject.php");
require_once("./lists.php");

error_log("KFK - Has loaded ".__FILE__);



function create_new_course($begin, $end,$type,$room=null,$id_subject=NULL){
	
	
	if(is_int($id_subject)){

 		$S=new Subject();
 		
 		$S->loadFromDB($id_subject);

		if(Database::currentDB()->sqlErrorMessage!="")return "";
		$C = new Course($S,$begin,$end);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		if($C->getSqlId()==null)return "";
	
	}else {
		$C = new Course(null,$begin,$end);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		if($C->getSqlId()==null)return "";
	}
	
	
	$C->setCourseType($id_subject);
	if(Database::currentDB()->sqlErrorMessage!="")return "";
	if(is_string($room))$C->setRoom($room);
	if(Database::currentDB()->sqlErrorMessage=="") return json_encode($C->to_array());
	else return "";
}





if(isset($_POST['action'])){
	if($_POST['action']=='create_course'){
			if(isset($_POST['id_subject']) && isset($_POST['room'])){
				$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],intval($_POST['id_subject']));
			}else if(isset($_POST['room'])){
				$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room']);	
			}
			else {
				$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']));
			}
		echo $result;
	}
}






?>
<?php
//require_once("classes/C_Course.php");
//require_once("classes/C_Subject.php");
require_once("./lists.php");

error_log("KFK - Has loaded ".__FILE__);
//echo "<pre>OK</pre>";


function create_new_course($begin, $end,$type,$room=null,$id_subject=NULL){
	
	
	if(is_int($id_subject)){
		//return "<pre>id_subject=".$id_subject."</pre>";
 		$S=new Subject();
 		
 		$S->loadFromDB($id_subject);
 		//return print_r($S,true);
 		//return addslashes(json_encode($S->to_array()))
		if(Database::currentDB()->sqlErrorMessage!="")return "<p> NO <p>";
		$C = new Course($S,$begin,$end);
		if(Database::currentDB()->sqlErrorMessage!="")return "<p> NO2 <p>";
		if($C->getSqlId()==null)return("<pre>C id null avec un id_subject</pre>");
	
	}else {
		return "<pre>id_subject null</pre>";
		$C = new Course(null,$begin,$end);
		if(Database::currentDB()->sqlErrorMessage!="")return "<pre> NO3 </pre>";
		if($C->getSqlId()==null)return "<pre>C id null (no id_subject)</pre>";
	}
	
	
	$C->setCourseType($id_subject);
	if(Database::currentDB()->sqlErrorMessage!="")return "<pre> NO4</pre>";
	if(is_string($room))$C->setRoom($room);
	if(Database::currentDB()->sqlErrorMessage=="") return json_encode($C->to_array());
	else return "<pre> NO5 </pre>";
}




if(isset($_POST)){


	if(isset($_POST['action'])){

		if($_POST['action']=='create_course'){
				if(isset($_POST['id_subject']))//echo "<p>".intval($_POST['id_subject'])."</p>";
		
				if(isset($_POST['id_subject']) && isset($_POST['room'])){
// 						echo "<pre>result1</pre>";
					$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],intval($_POST['id_subject']));
				}else if(isset($_POST['room'])){
// 					echo "<pre>result2</pre>";
// 					$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room']);
					
				}
				else {
// 					echo "<pre>result3</pre>";
// 					$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']));
				}
			echo $result;
			//echo("<p>ok</p>");
		}
	}

}




?>
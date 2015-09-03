<?php
require_once("classes/C_Course.php");
 require_once("classes/C_Subject.php");
 require_once("classes/C_Timetable.php");


error_log("KFK - Has loaded ".__FILE__);



 function create_new_course($begin, $end,$type,$room=null,$id_subject=NULL){
	
	if(is_int($id_subject)){

 		$S=new Subject();
 		
 		$S->loadFromDB($id_subject);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		$C = new Course($id_subject,$begin,$end);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		if($C->getSqlId()==null)return "";
	
	}else {

		$C = new Course(null,$begin,$end);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		if($C->getSqlId()==null)return "";
	}
	
	
	if(is_int($type))$C->setCourseType(intval($type));
	if(Database::currentDB()->sqlErrorMessage!="")return "";
	if(is_string($room))$C->setRoom($room);
	if(Database::currentDB()->sqlErrorMessage=="") return json_encode($C->to_array());
	else return "";
}


 function get_timetable_courses_between($id_calendar,$begin, $end){
	$result=array();
	if(is_int($id_calendar) && is_int($begin) && is_int($end)){
		$T=new Timetable();
		$T->loadFromDB($id_calendar);
		if(is_int($T->getSqlId())){
			$list=$T->getCoursesListBetween($begin,$end);
			//$list=$T->getCoursesList();
			foreach($list as $onecourseid){
				$C=new Course();
				$C->loadFromDB($onecourseid);
				if(is_int($C->getSqlId())){
					$result[]= json_encode($C->to_array());
				}
			}
		}
		$result=json_encode($result);
	
	}
	return $result;
}





if(isset($_POST['action'])){
	if($_POST['action']=='create_course'){
		$result="<pre>none</pre>";
		//echo $result;
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
	
	if($_POST['action']=='load_courses_between'){
		if(isset($_POST['begin']) && isset($_POST['end']) && isset($_POST['id']))
		{
			$result="<pre>none</pre>";
			$result= get_timetable_courses_between(intval($_POST['id']),intval($_POST['begin']),intval($_POST['end']));
			//$result= get_timetable_courses_between(1,0,50*365*24*60*60);
		
		}
		echo $result;
	}

}
	




?>
<?php
require_once("classes/C_Course.php");
 require_once("classes/C_Subject.php");
 require_once("classes/C_Timetable.php");


error_log("KFK - Has loaded ".__FILE__);



 function create_new_course($begin, $end,$type,$room=null,$id_subject=NULL,$id_group=NULL,$name=NULL){
	
	if(is_int($id_subject) && $id_subject>0){

 		$S=new Subject();
 		
 		$S->loadFromDB($id_subject);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		$C = new Course($id_subject,$begin,$end,$id_group);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		if($C->getSqlId()==null)return "";
	
	}else {

		$C = new Course(null,$begin,$end,$id_group);
		if(Database::currentDB()->sqlErrorMessage!="")return "";
		if($C->getSqlId()==null)return "";
	
	}
	
	
	if(is_int($type))$C->setCourseType($type);
	if(is_string($name))$C->setName($name);
	if(is_string($room))$C->setRoom($room);
	return json_encode($C->to_array());
	
}

function modify_course($id,$begin, $end,$type,$room=null,$id_subject=NULL,$name=NULL){

	$C = new Course();
	$C->loadFromDB($id);
	//$C = new Course(null,$begin,$end,$id_group);
	if(Database::currentDB()->sqlErrorMessage!="")return "";
	if($C->getSqlId()==null)return "";
	if(is_int($begin) && is_int($end)){
		$C->setBegin($begin);
		$C->setEnd($end);
	}
	
	$C->setSubject($id_subject);
	if(is_int($type))$C->setCourseType($type);
	if(is_string($name))$C->setName($name);
	if(is_string($room))$C->setRoom($room);
	return json_encode($C->to_array());
	
}

function get_course_from_id($id){
	if (is_int($id)){
		$C=new Course();
		$C->loadFromDB($id);
		if(is_int($C->getSqlId())){
			return json_encode($C->to_array());
		}
	}
	return "";
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

	}
	return json_encode($result);
}





if(isset($_POST['action'])){
	if($_POST['action']=='create_course'){
		$result="<pre>none</pre>";
		//echo $result;
			if(isset($_POST['id_subject']) && isset($_POST['room'])){
				if(isset($_POST['name']))
					$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],intval($_POST['id_subject']),NULL,$_POST['name']);
				else
					$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],intval($_POST['id_subject']));
			}else if(isset($_POST['room'])){
				if(isset($_POST['name']))$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],NULL,intval($_POST['id_group']),$_POST['name']);
				else $result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],NULL,intval($_POST['id_group']));
			}
			else {
				if(isset($_POST['name']))$result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),NULL,NULL,intval($_POST['id_group']),$_POST['name']);
				else $result=create_new_course(intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),NULL,NULL,intval($_POST['id_group']));
			}

// 			if(isset($_POST['repeat_until_date'])){
// 				$end=intval($_POST['end']);
// 				for($begin=intval($_POST['begin']);$begin<intval($_POST['repeat_until_date'])
// 			}
		echo $result;
	}
	
	if($_POST['action']=='modify_course' && isset($_POST['id'])){
		$result="<pre>none</pre>";
		//echo $result;
			if(isset($_POST['id_subject']) && isset($_POST['room'])){
				if(isset($_POST['name']))
					$result=modify_course(intval($_POST['id']),intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],intval($_POST['id_subject']),$_POST['name']);
				else
					$result=modify_course(intval($_POST['id']), intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],intval($_POST['id_subject']));
			}else if(isset($_POST['room'])){
				if(isset($_POST['name']))$result=modify_course(intval($_POST['id']), intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room'],NULL,$_POST['name']);
				else $result=modify_course(intval($_POST['id']),intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),$_POST['room']);
			}
			else {
				if(isset($_POST['name']))$result=modify_course(intval($_POST['id']), intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']),NULL,NULL,$_POST['name']);
				else $result=modify_course(intval($_POST['id']), intval($_POST['begin']),intval($_POST['end']),intval($_POST['type']));
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
	
	if($_POST['action']=='get_course_from_id'){
		if(isset($_POST['id']))
		{
			$result="<pre>none</pre>";
			$result= get_course_from_id(intval($_POST['id']));
			//$result= get_timetable_courses_between(1,0,50*365*24*60*60);
		
		}
		echo $result;
	}
	
	if($_POST['action']=='delete_course'){
		if(isset($_POST['id']))
		{
			//$result="<pre>none</pre>";
			$C=new Course();
			$C->loadFromDB(intval($_POST['id']));
			$C->removeFromDB();
			//$result= get_timetable_courses_between(1,0,50*365*24*60*60);
		
		}
		
	}

}
	




?>
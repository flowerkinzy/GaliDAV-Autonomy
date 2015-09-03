<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" type="text/css" href="scripts/jquery-ui/jquery-ui.css">
	<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">-->
	<link rel="stylesheet" type="text/css" href="./styles/styles.css">
	<link rel="stylesheet" type="text/css" href="./styles/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="./styles/fonts/css/font-awesome.css">
	<!--<script src="https://code.jquery.com/jquery-1.10.2.js"></script>-->
	<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui/jquery-ui.js"></script>
	<script src="config/calendar_vars.js"></script>
	<script src="styles/bootstrap.min.js"></script>
	
	<script src="scripts/calendar.js"></script>
	<script src="scripts/course.js"></script>
	<script src="scripts/d3.v3.min.js" charset="utf-8"></script>
	<title>Style2/Frame3</title>
	
</head>
<body>

       
	<?php error_reporting(E_ALL);
				include_once("functions/error_handling.php");
				require_once("config/path.php");
				
				?>
	<div id="wrap" class='fullspace'>
		<div class="navbar-fixed-top">
			<?php 	error_reporting(E_ALL); 
				include("./object_views/header_menu.php"); ?>
		</div>
		<div id="widget-zone-left">
			<?php	include("./object_views/widget_zone_left.php"); ?>
		</div>
		<div class="timetable fullspace" id="frame_timetable">
			<?php include("./object_views/calendar_frame.php"); ?>
		</div>
		<div id="footer-menu" class="navbar-fixed-bottom">
			<?php include("./object_views/footer_menu.php"); ?>
		</div>
	</div>
	
	<?php
		require_once("classes/C_Database.php");
		require_once("classes/C_Class.php");
		require_once("classes/C_Subject.php");
		require_once("classes/C_Timetable.php");
		require_once("classes/C_Course.php");
		

 		
		/*
		if(!Database::currentDB()->initialize()){
 			Database::currentDB()->showError();
 		}
		else{
			$INFO2=new C_Class("INFO2");
			$M=new Subject('Math',$INFO2->getSqlId());
			$begin=((9*60)+15)*60;//Par défaut, le jeudi 1er janvier 70 à 10h15
			//$begin=($timeintervalinmin*60)*(int)(time()/($timeintervalinmin*60));//heure courante. N'affiche rien si trop tôt/tard.
			$end=$begin+90*60; 

 			//$C=new Course($M->getSqlId(),$begin,$end); 
 			//$C->setRoom("C102");
 			//$C->setCourseType(CM);
 			

			
		}*/
		$begin=((9*60)+15)*60;//Par défaut, le jeudi 1er janvier 70 à 10h15
			//$begin=($timeintervalinmin*60)*(int)(time()/($timeintervalinmin*60));//heure courante. N'affiche rien si trop tôt/tard.
		$end=$begin+90*60; 
		//$C=new Course(1,$begin,$end); 
 			//$C->setRoom("C102");
 			//$C->setCourseType(CM);
	
	?>
</body>
</html>


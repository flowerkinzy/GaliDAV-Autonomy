<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" type="text/css" href="./styles/styles2.css">
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script src="scripts/course.js"></script>
	<title>Style2/Frame3</title>
</head>
<body>
	<div id="wrap" class='fullspace'>

	<div class="timetable fullspace" id="frame_timetable">
		<?php error_reporting(E_ALL); include("./object_views/calendar_frame3.php"); ?>
	</div>
	</div>
</body>
</html>
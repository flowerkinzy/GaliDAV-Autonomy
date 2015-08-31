<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!--<link rel="stylesheet" type="text/css" href="scripts/jquery-ui/jquery-ui.css">-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="./styles/styles2.css">
	<!--<script src="https://code.jquery.com/jquery-1.10.2.js"></script>-->
	<script src="scripts/jquery-ui/external/jquery/jquery.js"></script>
	<script src="scripts/jquery-ui/jquery-ui.js"></script>
	<script src="scripts/calendar.js"></script>
	<script src="scripts/course.js"></script>
	<script src="scripts/d3.v3.min.js" charset="utf-8"></script>
	<title>Log in to GaliDAV</title>
</head>
<body>
	<div id="logbox">
		<h2 type="log-welcome"> Welcome to GaliDAV </h2>
		<form method="post" action="#" enctype="multipart/form-data">
			<fieldset><legend> Log In </legend>
				<label for="username"> Username:</label>  <input name="username" type="text" id="username" /><br /><br />
				<label for="password"> Password:  </label><input type="text" name="password" id="password" /><br />
			</fieldset>

			<p><input type="submit" value="Connect" /></p>
		</form>
	</div>
</body>
</html>

<?php 
		function my_error_handler($errno , $errstr, $errfile , $errline){
			$out = "<div style = 'position:absolute;z-index:5;border-style:solid;background-color:#AA9999'>";
			$out .= "<p><b>$errstr </b> (n°$errno) <p>";
			$out .= "<p><i>Found in <b>$errfile</b> at line <b>$errline</b></i></p>";
			$out .= "</div>";
			echo $out;
			return true;
		}
		function my_fatal_error_handler(){
			if(!is_null($e = error_get_last()))
			{
				$out = "<div style = 'position:absolute;z-index:5;border-style:solid;background-color:#AA9999'>";
				$out .= "<p><b>".$e['message']."</b> (n°".$e['type'].")<p>";
				$out .= "<p><i>Found in <b>".$e['file']."</b> at line <b>".$e['line']."</b></i></p>";
				$out .= "</div>";
				echo $out;
				
			}
		}
		
		register_shutdown_function("my_fatal_error_handler");
	
?>
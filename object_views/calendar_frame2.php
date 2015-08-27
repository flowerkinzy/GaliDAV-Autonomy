<?php 
//include_once("./classes/C_Course.php");
//include_once("./classes/C_Subject.php");
//include_once("./functions/phpToJS.php");
error_reporting(E_ALL);
 ?>
<div id='frame_calendar_main_table' class=fullspace>

	<div id="frame_calendar_controls_row" class='fullspace-x no-margin-y'>
		<div class='fullspace-y'><p class='fullspace'>semaine<?php ?></p></div>
		<div class='fullspace-y'><p class='fullspace'> précédent /suivant </p></div>
	</div>
	
	<div class='fullspace-x'>
	<?php
	$hourmin=8;
	$hourmax=19;
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=30;
	echo "<table id='frame_calendar_core_table' class='calendar fullspace'>";
	for ($i = 1 ; $i <= (($hourmax-$hourmin)*60/$timeintervaldisplayinmin) -1 ; $i++){
		echo "<tr class='calendar fullspace-x'>";
		$res1=$hourmin+floor($i*$timeintervaldisplayinmin/60);
		$res2=($i*$timeintervaldisplayinmin)%60;
		$finalres=sprintf('%02d', $res1)." : ".sprintf('%02d', $res2);
		echo "<td class='fullspace-y hourcolumn'><div> $finalres</div></td>";
		for ($j_day=1; $j_day <= 5; $j_day++){
			echo "<td class='fullspace-y><div> $i </div> </td>";
		}
		echo "</tr>";
	}
	echo"</table>";

	?>
	</div>
</div>


<?php 
//$C=new Course(new Subject("Math"),time(),time()+90); //$encodedC=json_encode(object_to_array($C));
?>
<script> 
test(); 
//displayNewCourseElement(2,"MATH CM1","9h-12h","C102");
//displayNewCourseElementClass( ...);
</script>
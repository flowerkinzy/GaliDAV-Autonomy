<?php 
include_once("./classes/C_Course.php");
include_once("./classes/C_Subject.php");
error_reporting(E_ALL);
 ?>
<div id='frame_calendar_main_table' class=fullspace>

	<div id="frame_calendar_controls_row" class='fullspace-x no-margin-y'>
		<div class='fullspace-y'><p class='fullspace'>semaine<?php ?></p></div>
		<div class='fullspace-y'><p class='fullspace'> précédent /suivant </p></div>
	</div>
	
	<div id='frame_calendar_core_table' class='fullspace-x'>

	<?php
	$hourmin=8;
	$hourmax=18;
	$beginmin=30;
	$endmin=45;
	
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=30;
	echo "<table id='calendar_core_table' class='calendar fullspace-y'>";
	for ($beginH = $hourmin ; $beginH <= $hourmax ; $beginH++){
		$bm=0;
		$em=60;
		if($beginH==$hourmin){
			$bm=$beginmin;
		}
		if($beginH==$hourmax){
			$em=$endmin;
		}
		for($beginM=$bm; $beginM < $em; $beginM=$beginM+$timeintervalinmin){
			$endM=$beginM+$timeintervalinmin;
			echo "<tr class='calendar fullspace-x' begin_hour=$beginH begin_min=$beginM end_min=$endM >";
			$finalres=sprintf('%02d', $beginH)." : ".sprintf('%02d', $beginM);
			echo "<td class='fullspace-y hourcolumn' begin_hour=$beginH begin_min=$beginM end_min=$endM><div> $finalres</div></td>";
			for ($j_day=0; $j_day <= 4; $j_day++){
				echo "<td class='fullspace-y daycolumn' weekday=$j_day begin_hour=$beginH begin_min=$beginM end_min=$endM>
					<div>  </div> </td>";
			}
			echo "</tr>";
		}
	}
	echo"</table>"; 	
	?>
	
	</div>
</div>
<script>
<?php
	//$D=get_date(time()/($timeintervalinmin*60));
	//$begin2=get_date(time()/($timeintervalinmin*60));
	//$end2=get_date((time()/($timeintervalinmin*60))+90*60);
	$begin=(int)floor(time()/($timeintervalinmin*60));
	$begin=time();
	$end=$begin+90*60; 
	$C=new Course(new Subject('Math'),$begin,$end); 
	$C->setRoom("C102");
	$C->setCourseType(CM);
	echo "displayNewCourseElementClass('".addslashes(json_encode($C->to_array()))."');";
?>
</script>
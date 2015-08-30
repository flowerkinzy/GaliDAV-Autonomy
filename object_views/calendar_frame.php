<?php 
include_once("classes/C_Class.php");
include_once("./classes/C_Course.php");
include_once("./classes/C_Subject.php");
error_reporting(E_ALL);
error_log("KFK - Has loaded ".__FILE__);
 ?>
<div id='frame_calendar_main_table' class=fullspace>

	<div id="frame_calendar_controls_row" class='fullspace-x no-margin-y'>
		<div class='fullspace-y'><p class='fullspace'>semaine<?php ?></p></div>
		<div class='fullspace-y'><p class='fullspace'> pr&eacute;c&eacute;dent /suivant </p></div>
	</div>
	

	<div id='frame_calendar_core_table' class='fullspace-x'>

	<?php
	$hourmin=8;
	$hourmax=18;
	$beginmin=30;
	$endmin=45;

	$days=array(1 => 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi');
	
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=30;

	echo "<table id='calendar_core_table' class='calendar fullspace-y'>";
	echo "<tr class='calendar fullspace-x'>";
	for ($j_day=0; $j_day <= 5; $j_day++){
		if($j_day>0)$weekDayDisplay=sprintf('%s', $days[$j_day]);
		else $weekDayDisplay="";
		echo "<td class='fullspace-y'> <div> $weekDayDisplay </div> </td>";
	}
	echo "</tr>";
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

<?php
	$begin=((9*60)+15)*60;//Par défaut, le jeudi 1er janvier 70 à 10h15
	//$begin=($timeintervalinmin*60)*(int)(time()/($timeintervalinmin*60));//heure courante. N'affiche rien si trop tôt/tard.
	$end=$begin+90*60; 
	$INFO2=new C_Class("INFO2");
	$M=new Subject('Math',$INFO2);
	//$C=new Course($M,$begin,$end); 
	//$C->setRoom("C102");
	//$C->setCourseType(CM);
	//echo "<script>displayNewCourseElementClass('".addslashes(json_encode($C->to_array()))."');</script>";
?>


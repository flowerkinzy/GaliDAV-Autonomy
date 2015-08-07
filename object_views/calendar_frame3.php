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
	
	<div class='fullspace-x'>
	<?php
	/* 
	$hourmin=8;
	$hourmax=19;
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=30;
	echo "<table id='frame_calendar_core_table' class='calendar fullspace'>";
	for ($i = 1 ; $i <= (($hourmax-$hourmin)*60/$timeintervaldisplayinmin) -1 ; $i++){
		echo "<tr class='calendar fullspace-x'>";
		$Begin_H=$hourmin+floor($i*$timeintervaldisplayinmin/60);
		$Begin_M=($i*$timeintervaldisplayinmin)%60;
		$End_M=$Begin_M+$timeintervaldisplayinmin-1;
		$finalres=sprintf('%02d', $Begin_H)." : ".sprintf('%02d', $Begin_M);
		echo "<td class='fullspace-y hourcolumn' begin_hour=$Begin_H begin_min=$Begin_M end_min=$End_M><div> $finalres</div></td>";
		for ($j_day=0; $j_day <= 4; $j_day++){
			echo "<td class='fullspace-y daycolumn' weekday=$j_day begin_hour=$Begin_H begin_min=$Begin_M end_min=$End_M>
				<div>  </div> </td>";
		}
		echo "</tr>";
	}
	echo"</table>"; 
	*/
	?>
	<?php
	$hourmin=8;
	$hourmax=19;
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=30;
	echo "<table id='frame_calendar_core_table' class='calendar fullspace'>";
	for ($i = 1 ; $i <= (($hourmax-$hourmin)*60/$timeintervalinmin) -1 ; $i++){
		echo "<tr class='calendar fullspace-x'>";
		$Begin_H=$hourmin+floor($i*$timeintervalinmin/60);
		$Begin_M=($i*$timeintervalinmin)%60;
		$End_M=$Begin_M+$timeintervalinmin-1;
		$finalres=sprintf('%02d', $Begin_H)." : ".sprintf('%02d', $Begin_M);
		echo "<td class='fullspace-y hourcolumn' begin_hour=$Begin_H begin_min=$Begin_M end_min=$End_M><div> $finalres</div></td>";
		for ($j_day=0; $j_day <= 4; $j_day++){
			echo "<td class='fullspace-y daycolumn' weekday=$j_day begin_hour=$Begin_H begin_min=$Begin_M end_min=$End_M>
				<div>  </div> </td>";
		}
		echo "</tr>";
	}
	echo"</table>"; 
	
	
	?>
	
	</div>
</div>
<script>
<?php
	$begin=(int)floor(time()/($timeintervalinmin*60));
	$end=$begin+90*60; 
	$C=new Course(new Subject('Math'),$begin,$end); 
	$C->setRoom("C102");
	$C->setCourseType(CM);
	echo "displayNewCourseElementClass('".addslashes(json_encode($C->to_array()))."');";
?>
</script>
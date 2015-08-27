<div id='frame_calendar_main_table' class=fullspace>

	<div id="frame_calendar_controls_row" class='fullspace-x no-margin-y'>
	<table class='fullspace'><tr class='fullspace'><th class='fullspace-y'>semaine<?php ?></th>
	<td class='fullspace-y'> précédent /suivant </td></tr></table>
	</div>
	<div id="frame_calendar_core_row" class='fullspace-x no-margin-y'>
	<?php
	/*$hourmin=8;
	$hourmax=19;
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=30;
	echo "<td><table class='calendar calendar_time'>";
	for ($i = 1 ; $i <= ($hourmax-$hourmin)*60/$timeintervaldisplayinmin ; $i++)
			if($i%($timeintervaldisplayinmin/$timeintervalinmin)==0)
				echo "<tr><td>$i</td></tr>";
			else
				echo "<tr><td>' '</td></tr>";
	echo "</table></td>";	
	for ($i_day = 1 ; $i_day <= 5 ; $i_day++){
		echo "<td><table class='calendar calendar_day'>";
		for ($j_hour = 1 ; $j_hour <= ($hourmax-$hourmin)*60/$timeintervaldisplayinmin ; $j_hour++)
			echo "<tr><td>$j_hour</td></tr>";
		echo "</table></td>";
	}*/

	?>
	
	<?php
	$hourmin=8;
	$hourmax=19;
	$timeintervalinmin=15;
	$timeintervaldisplayinmin=15;
	echo "<table id='frame_calendar_core_table' class='calendar fullspace'>";
	for ($i = 1 ; $i <= ($hourmax-$hourmin)*60/$timeintervaldisplayinmin ; $i++){
		echo "<tr class='calendar fullspace-x'>";
		$res1=$hourmin+floor($i*$timeintervaldisplayinmin/60);
		$res2=($i*$timeintervaldisplayinmin)%60;
		$finalres=sprintf('%02d', $res1)." : ".sprintf('%02d', $res2);
		echo "<td class=fullspace-y> $finalres</td>";
		for ($j_day=1; $j_day <= 5; $j_day++){
			echo "<td class=fullspace-y> $i </td>";
		}
		echo "</tr>";
	}
	echo"</table>";

	?>
	</div>
</div>


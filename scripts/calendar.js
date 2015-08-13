var hourmin=8;
var hourmax=19;
var timeintervalinmin=15;
var timeintervaldisplayinmin=30;
 $( document ).ready(function() {
	$("tr.calendar").on("click","td.hourcolumn",function (){
		if($(this).parent().children().children().children().length==0){ //Checks if there is no activity starting at that time
			$button="<button style='padding:0;cursor:default;' ";
			$button=$button+"begin_hour="+$(this).attr("begin_hour")+" ";
			$button=$button+"begin_min="+$(this).attr("begin_min")+" ";
			$button=$button+"end_min="+$(this).attr("end_min")+" ";
			$button=$button+"> \> </button>";
			$(this).parent().children().children().hide();
			$("#calendar_core_table").append($button);
		
			adapt_button_position();
			
			$( "button[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]" ).on("click",function(){
				$("tr.calendar[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]").children().children().show();
				$(this).remove();
			});
		}
	});
	
	
//FIN documentReady
	});
	
	
function adapt_button_position(){
	B=$("button").detach();
		for(i=0;i<B.length;i++){

			$(B[i]).appendTo("#calendar_core_table");
			$(B[i]).position({
				my:	"right center",
				at: "left center",
				of: "tr.calendar[begin_hour="+$(B[i]).attr("begin_hour")+"][begin_min="+$(B[i]).attr("begin_min")+"]"
			});
		}
	
}
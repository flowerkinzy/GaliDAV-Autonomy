
 $( document ).ready(function() {
	
	createtimetablemenu();
	$("#widget-zone-left").on("click","h3.option_group,li.option_group",function(){

		CURRENT_GROUP_ID=parseInt($(this).attr("id"));
		CURRENT_CALENDAR_ID=parseInt($(this).attr("id_timetable"));
		//console.log("id_timetable="+CURRENT_CALENDAR_ID+" / id_group="+CURRENT_GROUP_ID);
		$("#name_timetable").html($(this).text());
		$("div.course").remove();
		$("#frame_timetable").show();
		$("#button_next_week,#button_previous_week").show();
		loadTimetableForWeek($(this).attr("id_timetable"),FIRST_DAY_OF_WEEK_UTC);
		if($("#button_modify_timetable,#button_validate_timetable").get(0)!=undefined)$("#button_modify_timetable,#button_validate_timetable").show();
		stopModification();
		
	});
	
/*****************************
 * *****FIN documentReady*****
 * ***************************/

});
	
	
function createtimetablemenu(){
	$.get("functions/lists.php",{action:"get_list_classes_with_linked_groups"},function(data){
		try{
			//console.log("data="+data);
			var obj=jQuery.parseJSON(data);
			var menu=$("<div id='div_menu_classes'></div>");
			
			for(i=0;i<obj.length;i++){
				var O=jQuery.parseJSON(obj[i]);
				$(menu).append("<h3 class='option_group' id="+O.id+" id_timetable="+O.id_timetable+">"+O.name+"</h3>");
				
					var list=$("<ul></ul>");
					//console.log("obj["+i+"]="+obj[i]);
					try{
						//console.log("linked_groups="+jQuery.parseJSON(O.linked_groups));
						if(jQuery.parseJSON(O.linked_groups)!=undefined){
							
							var obj2=jQuery.parseJSON(O.linked_groups);
							
							for(j=0;j<obj2.length;j++){
								var obj3=jQuery.parseJSON(obj2[j]);
								$(list).append("<li class='option_group' id="+obj3.id+" id_timetable="+obj3.id_timetable+">"+obj3.name+"</li>");
							}
						}
					}catch(err){
						
					}
				$(menu).append($(list));
					
			}
			$("#widget-zone-left").append($(menu));
			$("#div_menu_classes").accordion({event:"mouseover"});
		}catch(err){
			$("body").append(data)
		}
	});
	
	
}
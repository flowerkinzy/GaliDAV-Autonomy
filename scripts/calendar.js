
 $( document ).ready(function() {
	$("tr.calendar").on("dblclick","td.hourcolumn",function (){
		if($(this).parent().children().children().children().length==0){ //Checks if there is no activity starting at that time
			$button="<button style='padding:0;cursor:default;' ";
			$button=$button+"begin_hour="+$(this).attr("begin_hour")+" ";
			$button=$button+"begin_min="+$(this).attr("begin_min")+" ";
			$button=$button+"end_min="+$(this).attr("end_min")+" ";
			$button=$button+"> \> </button>";
			adaptCoursesHeightAfterHidingRow($(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_min"),$(this).height());
			//rowheight=$(this).height();
			$(this).parent().children().children().hide();
			$("#calendar_core_table").append($button);
			
		
			adapt_button_position();
			
			$( "button[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]" ).on("click",function(){
				$("tr.calendar[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]").children().children().show();
				$(this).remove();
				adapt_button_position();
				adaptCoursesHeightAfterShowingRow($(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_min"),
					$("tr.calendar[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]").height());
			});
		}
	});
	
	$("tr.calendar").on("click","td.hourcolumn",function (){	
		$(this).children().toggle();
	});
	
	$("td.daycolumn>div").on("click",function(){
		console.log("on click");
		displayFormNewEvent($(this).parent().attr("begin_hour"),$(this).parent().attr("begin_min"));
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

function getCoursesOfDayHappeningAt(weekday,beginH,beginM,endM){
	//criteria1="[weekday="+weekday+"]";
	coursesOfDayList=$("td.daycolumn[weekday="+weekday+"] div.course").get();
	if(coursesOfDayList.length>0)console.log("$(td.daycolumn[weekday="+weekday+"] div.course).get().length="+coursesOfDayList.length);
	//console.dir(coursesOfDayList);
	var resultingList=[];
	var ok=false;
	for(i=0;i<coursesOfDayList.length;i++){
		ibeginH=parseInt($(coursesOfDayList[i]).attr("begin_hour"));
		iendH=parseInt($(coursesOfDayList[i]).attr("end_hour"));
		ibeginM=parseInt($(coursesOfDayList[i]).attr("begin_min"));
		iendM=parseInt($(coursesOfDayList[i]).attr("end_min"));
		console.log("BeginH:"+beginH+"/BeginM"+beginM+"/EndM:"+endM);
		console.log("ibeginH:"+ibeginH+"/ibeginM"+ibeginM+"/iendH:"+iendH+"/iendM:"+iendM);
		if(ibeginH<=beginH){
			if(iendH>=beginH){
				if(ibeginH==beginH){
					if(ibeginM==beginM)
						ok=true;
					else if(ibeginM<beginM){
						if(iendH>beginH)
							ok=true;
						else if(iendM>=endM)
							ok=true;
						else console.log("false4");
					}else console.log("false3");
				}
				else{
					if(iendH>beginH)
						ok=true;
					else if(iendH==beginH){
						 if(iendM>=endM)
							ok=true;
						else console.log("false6");
					}else console.log("false5");
				}
			}else console.log("false2");
		}else console.log("false1");
		if(ok){
			console.log("ok");
			resultingList.push(coursesOfDayList[i]);
		}else console.log("false");
	}
	if(resultingList!=[] && resultingList.length>0)console.log("Day:"+weekday+" resultingList.length="+resultingList.length);
	return resultingList;
}

function getCoursesOfWeekHappeningAt(beginH,beginM,endM){
	var resultingList=[];
	var j_day;
	var list=[];
	for(j_day=0;j_day<=4;j_day++){
		list=getCoursesOfDayHappeningAt(j_day,beginH,beginM,endM);
		if(list!=[] && list.length>0){
			console.log("list.length=="+list.length);
			console.dir(list);
		}
		resultingList=resultingList.concat(list);
	}
	if(resultingList!=[]){
		console.log("Week: resultingList.length="+resultingList.length);
		console.dir(resultingList);
	}
	return resultingList;
}

function adaptCoursesHeightAfterHidingRow(beginH,beginM,endM, rowHeight){
	var list=getCoursesOfWeekHappeningAt(beginH,beginM,endM);
	var i;
	var currentHeight;
	var finalHeight;
	console.log("Final list.length="+list.length);
	for(i=0;i<list.length;i++){
		currentHeight=$(list[i]).height();
		finalHeight=currentHeight-rowHeight;
		console.log("rowHeight="+rowHeight+"; current="+currentHeight+";")
		console.log("final height:"+finalHeight);
		$(list[i]).height(finalHeight);
		$(list[i]).css("max-height",finalHeight+"px");
		$(list[i]).css("min-height",finalHeight+"px");
	
	}
}
function adaptCoursesHeightAfterShowingRow(beginH,beginM,endM, rowHeight){
	var list=getCoursesOfWeekHappeningAt(beginH,beginM,endM);
	var i;
	var currentHeight;
	var finalHeight;
	console.log("Final list.length="+list.length);
	for(i=0;i<list.length;i++){
		currentHeight=$(list[i]).height();
		finalHeight=currentHeight+rowHeight;
		console.log("rowHeight="+rowHeight+"; current="+currentHeight+";")
		console.log("final height:"+finalHeight);
		$(list[i]).height(finalHeight);
		$(list[i]).css("max-height",finalHeight+"px");
		$(list[i]).css("min-height",finalHeight+"px");
	
	}
}

function displayFormNewEvent(BeginH,BeginM){
	$("#newOrModifyCourse").remove();
	
	$(createFormNewEvent(BeginH,BeginM)).appendTo("#wrap");
	
	$("#newOrModifyCourse").dialog();
	
	$("#newOrModifyCourse").parent().css("width","50%");
	$("#newOrModifyCourse").parent().css("height","80%");
	$("#newOrModifyCourse").parent().css("top","25%");
	$("#newOrModifyCourse").parent().css("left","10%");
}

function createFormNewEvent(BeginH,BeginM){
	var div=$("<div id=newOrModifyCourse></div>"); //attributes To complete
	var form=$("<form></form>");
	
	var divBegin=$("<div class=timespinnersframe></div>");
	var hourpickerB=$("<input required >");
	var minpickerB=$("<input required >");
	$(divBegin).append(hourpickerB);
	
	$(divBegin).append(minpickerB);
	$(form).append(divBegin);
	
	var divEnd=$("<div class=timespinnersframe></div>");
	var EndHdefault=Math.floor(((parseInt(BeginH)*60)+parseInt(BeginM)+90)/60)
	var EndMdefault=Math.floor((parseInt(BeginH)*60)+parseInt(BeginM)+90)%60;
	
	var hourpickerE=$("<input required >");
	var minpickerE=$("<input required >");
	
	$(divEnd).append(hourpickerE);
	$(divEnd).append(minpickerE);	
	$(form).append(divEnd);
	
	var divSubject=$("<select name='subject' id='select_choose_subject'><option value='A'>A</select>");
	$(divSubject).selectmenu({
		appendTo:"#div_select_choose_subject"
	});
	
	//$(divSubject).wrap("<div id='div_select_choose_subject'></div>");
	$(form).append(divSubject);
	
	$(form).append("<div><input type='submit'/></div>");
	$(div).append(form);
	
	$(minpickerB).spinner({
		step:15,
		incremental:false,
		min:0,
		max:45
	});
	
	$(hourpickerB).spinner({
		min:HOUR_MIN,
		max:HOUR_MAX,
		stop: function( event, ui ) {
			if($(hourpickerB).spinner("value")==HOUR_MAX){
				$(minpickerB).spinner("option","max",(END_MIN-TIME_INTERVAL_IN_MIN));
				if($(minpickerB).spinner("value")>(END_MIN-TIME_INTERVAL_IN_MIN))
					$(minpickerB).spinner("value",(END_MIN-TIME_INTERVAL_IN_MIN));
			}else{
				$(minpickerB).spinner("option","max",45);
			}
			if($(hourpickerB).spinner("value")==HOUR_MIN){
				if($(minpickerB).spinner("value")<BEGIN_MIN)
					$(minpickerB).spinner("value",BEGIN_MIN);
			}else{
				$(minpickerB).spinner("option","min",0);
			}
		}
	});
	if(BeginH==HOUR_MIN)$(minpickerB).spinner("option","min",BEGIN_MIN);
	if(BeginH==HOUR_MAX)$(minpickerB).spinner("option","max",(END_MIN-TIME_INTERVAL_IN_MIN));
	$(minpickerE).spinner({
		step:15,
		incremental:false,
		min:0,
		max:45
	});
	$(hourpickerE).spinner({
		min:HOUR_MIN,
		max:HOUR_MAX,
		stop: function( event, ui ) {
			if($(hourpickerE).spinner("value")==HOUR_MAX){
				$(minpickerE).spinner("option","max",END_MIN);
				if($(minpickerE).spinner("value")>END_MIN)
					$(minpickerE).spinner("value",END_MIN);
			}else{
				$(minpickerE).spinner("option","max",45);
			}
			if($(hourpickerE).spinner("value")==HOUR_MIN){
				$(minpickerE).spinner("option","min",(BEGIN_MIN+TIME_INTERVAL_IN_MIN));
				if($(minpickerE).spinner("value")>(BEGIN_MIN+TIME_INTERVAL_IN_MIN))
					$(minpickerE).spinner("value",(BEGIN_MIN+TIME_INTERVAL_IN_MIN));
			}else{
				$(minpickerE).spinner("option","min",0);
			}
		}
	});
	//if(EndH==HOUR_MIN)$(minpickerB).spinner("option","min",(BEGIN_MIN+TIME_INTERVAL_IN_MIN)%60);
	if(EndHdefault==HOUR_MAX)$(minpickerB).spinner("option","max",END_MIN);
	
	
	$(hourpickerB).spinner("value",BeginH);
	$(minpickerB).spinner("value",BeginM);
	$(hourpickerE).spinner("value",EndHdefault);
	$(minpickerE).spinner("value",EndMdefault);	
	
	
	return div;
}

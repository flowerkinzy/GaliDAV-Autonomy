var CURRENT_GROUP_ID=0;
var CURRENT_CALENDAR_ID=0;
var FIRST_DAY_OF_WEEK_UTC=Date.UTC(2015, 8, 14, 0, 0, 0, 0) + (new Date().getTimezoneOffset()*60*1000); 
 $( document ).ready(function() {
	$("tr.calendar").on("dblclick","td.hourcolumn",function (){
		if($(this).parent().children().children().children().length==0){ //Checks if there is no activity starting at that time
			//console.log("Double-click on calendar cell");
			var button="<button class='hide_row' style='padding:0;cursor:default;' ";
			button=button+"begin_hour="+$(this).attr("begin_hour")+" ";
			button=button+"begin_min="+$(this).attr("begin_min")+" ";
			button=button+"end_min="+$(this).attr("end_min")+" ";
			button=button+"> \> </button>";
		//	console.log("button="+button);
			adaptCoursesHeightAfterHidingRow($(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_min"),$(this).height());
			
			$(this).parent().children().children().hide();
			$("#calendar_core_table").append(button);
			
		
			adapt_button_position();
			
			$( "button[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]" ).on("click",function(){
				$("tr.calendar[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]").children().children().show();
				//$(this).remove();
				$( "button[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]" ).remove();
				adapt_button_position();
				adaptCoursesHeightAfterShowingRow($(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_min"),
					$("tr.calendar[begin_hour="+$(this).attr("begin_hour")+"][begin_min="+$(this).attr("begin_min")+"]").height());
			});

		}
	});
	
	$("tr.calendar").on("click","td.hourcolumn",function (){	
		$(this).children().toggle();
	});
	
 	
	
	$("#button_next_week").on("click",function(){
			$("div.course").remove();
			FIRST_DAY_OF_WEEK_UTC=FIRST_DAY_OF_WEEK_UTC+(7*24*60*60*1000);
			loadTimetableForWeek(CURRENT_CALENDAR_ID,FIRST_DAY_OF_WEEK_UTC);
	});
	$("#button_previous_week").on("click",function(){
			$("div.course").remove();
			FIRST_DAY_OF_WEEK_UTC=FIRST_DAY_OF_WEEK_UTC-(7*24*60*60*1000);
			loadTimetableForWeek(CURRENT_CALENDAR_ID,FIRST_DAY_OF_WEEK_UTC);
	});
	
	$("div.course,td.daycolumn>div").on("click",function(){
		alert("Vous n'avez pas les droits pour modifier\nou vous n'avez pas activé la modification");
	});
	
	$("#footer-menu").on("click","#button_modify_timetable",function(){
		$(this).after("<a class='btn col-md-2'  id='button_stop_modify' > Fermer le mode modification</a>");
		$(this).remove();
		$("#button_validate_timetable").remove();
		
		$("div.course").off("click");
		$("td.daycolumn>div").off("click");
		$("td.daycolumn>div").on("click",function(){
			//console.log("click on cell");
			displayFormNewEvent($(this).parent().attr("begin_hour"),$(this).parent().attr("begin_min"),$(this).parent().attr("weekday"),CURRENT_GROUP_ID);
		});
		
		$("div.course").on("click",function(event){
			
			if(parseInt($(this).attr("id_group"))!=CURRENT_GROUP_ID);//alert("Ce cours dépend d'un autre groupe");
			else{
				event.stopPropagation();
				displayFormModifyEvent($(this).attr("id"),$(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_hour"),$(this).attr("end_min"),$(this).parent().parent().attr("weekday"));			
			}
				
			});
		
		//TODO insérer bouton Fermer le mode modification
	});
	
	$("#footer-menu").on("click","#button_stop_modify",function(){stopModification();});
	
	
	$("#frame_timetable").hide();
	$("#button_next_week,#button_previous_week").hide();
	$("#button_modify_timetable,#button_validate_timetable").hide();
	//loadTimetableForWeek(CURRENT_CALENDAR_ID,FIRST_DAY_OF_WEEK_UTC);

	
/*****************************
 * *****FIN documentReady*****
 * ***************************/
});
	
	
function adapt_button_position(){
	B=$("button.hide_row").detach();
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

	coursesOfDayList=$("td.daycolumn[weekday="+weekday+"] div.course").get();
	//if(coursesOfDayList.length>0)console.log("$(td.daycolumn[weekday="+weekday+"] div.course).get().length="+coursesOfDayList.length);
	//console.dir(coursesOfDayList);
	var resultingList=[];
	var ok=false;
	for(i=0;i<coursesOfDayList.length;i++){
		ibeginH=parseInt($(coursesOfDayList[i]).attr("begin_hour"));
		iendH=parseInt($(coursesOfDayList[i]).attr("end_hour"));
		ibeginM=parseInt($(coursesOfDayList[i]).attr("begin_min"));
		iendM=parseInt($(coursesOfDayList[i]).attr("end_min"));
		//console.log("BeginH:"+beginH+"/BeginM"+beginM+"/EndM:"+endM);
		//console.log("ibeginH:"+ibeginH+"/ibeginM"+ibeginM+"/iendH:"+iendH+"/iendM:"+iendM);
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
						else ;//console.log("false4");
					}else ;// console.log("false3");
				}
				else{
					if(iendH>beginH)
						ok=true;
					else if(iendH==beginH){
						 if(iendM>=endM)
							ok=true;
						else ;//console.log("false6");
					}else ;// console.log("false5");
				}
			}else ;//console.log("false2");
		}else ;//console.log("false1");
		if(ok){
			//console.log("ok");
			resultingList.push(coursesOfDayList[i]);
		}//else console.log("false");
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
			//console.log("list.length=="+list.length);
			//console.dir(list);
		}
		resultingList=resultingList.concat(list);
	}
	if(resultingList!=[]){
		//console.log("Week: resultingList.length="+resultingList.length);
		//console.dir(resultingList);
	}
	return resultingList;
}

function adaptCoursesHeightAfterHidingRow(beginH,beginM,endM, rowHeight){
	var list=getCoursesOfWeekHappeningAt(beginH,beginM,endM);
	var i;
	var currentHeight;
	var finalHeight;
	//console.log("Final list.length="+list.length);
	for(i=0;i<list.length;i++){
		currentHeight=$(list[i]).height();
		finalHeight=currentHeight-rowHeight;
		//console.log("rowHeight="+rowHeight+"; current="+currentHeight+";")
		//console.log("final height:"+finalHeight);
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
		//console.log("rowHeight="+rowHeight+"; current="+currentHeight+";")
		//console.log("final height:"+finalHeight);
		$(list[i]).height(finalHeight);
		$(list[i]).css("max-height",finalHeight+"px");
		$(list[i]).css("min-height",finalHeight+"px");
	
	}
}


/*************************************
 * *********Create an Event *********
 * ***********************************/
function displayFormNewEvent(BeginH,BeginM,weekday,id_group){
	$("#newOrModifyCourse").remove();
	
	$(createFormNewEvent(BeginH,BeginM,weekday)).appendTo("body");
	
	
	$("#newOrModifyCourse").dialog({
		title:"Nouveau cours",
		minWidth:"50%",
		minHeight:"80%",
		width:"50%",
		//height:"80%",
		top:"25%",
		left:"10%",
		resizable:false,
		modal:true
	});
	$("#button_validate_new_event").on("click",function(event){
		event.stopPropagation();
		event.preventDefault;
		console.log("FIRST_DAY_OF_WEEK_UTC unix ="+Math.floor(FIRST_DAY_OF_WEEK_UTC/1000));
		var beginUTC=Math.floor(FIRST_DAY_OF_WEEK_UTC/1000)+($("#input_weekday").val()*24*60*60)+($("#input_pick_hour_begin").spinner("value")*60*60);
		beginUTC += ($("#input_pick_min_begin").spinner("value")*60);
		var endUTC=Math.floor(FIRST_DAY_OF_WEEK_UTC/1000)+($("#input_weekday").val()*24*60*60)+($("#input_pick_hour_end").spinner("value")*60*60);
		endUTC += ($("#input_pick_min_end").spinner("value")*60);
		//var param={begin: beginUTC,end: endUTC, type: $("#select_choose_type").val()};
		var param=new Object();
		param.action="create_course";
		param.begin=beginUTC;
		param.end=endUTC;
		param.type=$("#select_choose_type").val();
		param.room=$("#input_room").val();
		if($("#input_name").val()!="")param.name=$("#input_name").val();
		param.id_group=id_group;
		if($("#select_choose_subject").val()>0)param.id_subject=$("#select_choose_subject").val();
		console.log("button_validate_new_event/param=..."); console.dir(param);
 		$("#newOrModifyCourse").dialog("close");
	
		//TODO check there is no blocking course
		$.post("functions/courses_functions.php",
		      param,
			function(data)
			{
				//console.log("create_course: data="+data+"...");
				//console.dir(data);
				try{
					var obj=jQuery.parseJSON(data);
					displayNewCourseElementClass(data);
					$("div.course[id="+obj.sqlId+"]").on("click",function(event){
						if(parseInt($(this).attr("id_group"))!=CURRENT_GROUP_ID);//alert("Ce cours dépend d'un autre groupe");
						else{
							event.stopPropagation();
							displayFormModifyEvent($(this).attr("id"),$(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_hour"),$(this).attr("end_min"),$(this).parent().parent().attr("weekday"));				
						}
							
					});
				}catch(err){
					$("body").append(data)
				}

			}
		  );
	});
	$("#input_until_date").next().on("click",function(data){
		console.log("effacer date");
		$("#input_until_date").datepicker("setDate",null);
	});

}


function createFormNewEvent(BeginH,BeginM,weekday){
	var div=$("<div id=newOrModifyCourse></div>"); //attributes To complete
	var form=$("<div></div>");
	$(form).append("<input id='input_weekday' type=hidden name='weekday' value="+weekday+" />");
	var divBegin=$("<div class=timespinnersframe></div>");
	var hourpickerB=$("<input onblur='checkHourField(this)' id='input_pick_hour_begin' required >");
	var minpickerB=$("<input onblur='checkMinField(this)' id='input_pick_min_begin' required >");
	$(divBegin).append("<p class='formlabel'>Heure de début:</p>");
	$(divBegin).append(hourpickerB);
	
	$(divBegin).append(minpickerB);
	$(form).append(divBegin);
	
	var divEnd=$("<div class=timespinnersframe></div>");
	var EndHdefault=Math.floor(((parseInt(BeginH)*60)+parseInt(BeginM)+90)/60)
	var EndMdefault=Math.floor((parseInt(BeginH)*60)+parseInt(BeginM)+90)%60;
	
	var hourpickerE=$("<input onblur='checkHourField(this)' id='input_pick_hour_end' required >");
	var minpickerE=$("<input onblur='checkMinField(this)' id='input_pick_min_end' required >");
	$(divEnd).append("<p class='formlabel'>Heure de fin:</p>");
	$(divEnd).append(hourpickerE);
	$(divEnd).append(minpickerE);
	var divUntildate=$("<span id='div_input_until_date'></span>");
	$(divUntildate).append("<span class='formlabel'> Répéter ce cours jusqu'au:</span>");
	$(divUntildate).append("<input id='input_until_date'/>");
	$(divUntildate).append("<button>x</button>");
	$(divEnd).append($(divUntildate));
	$(form).append(divEnd);
	
	var divSubject=$("<select name='subject' id='select_choose_subject'></select>");
	$.get("functions/lists.php",{action:"get_lists_subjects", id_group:CURRENT_GROUP_ID},function(data){
		$(divSubject).append("<option value=0 >--");
		$(divSubject).append(data);
	});
	$(divSubject).wrap("<div id='div_select_choose_subject'></div>");
	$(divSubject).parent().prepend("<p class='formlabel'>Choisir une matière:</p>");
	$(form).append($(divSubject).parent());
	
	
	
	
	
	var selectType=$("<select name='type' id='select_choose_type'></select>");
	for(i=0;i<NUMBER_OF_COURSES_TYPES;i++){
		$(selectType).append("<option value="+i+" >"+getTypeName(i));
	}
	$(selectType).wrap("<div id='div_select_choose_type'></div>");
	$(selectType).parent().prepend("<p class='formlabel'>Type de cours:</p>");
	$(form).append($(selectType).parent());
	
	$(form).append("<div id='div_input_name'><p class='formlabel'>Intitulé Supplémentaire:</p><input id='input_name' name='name' type='text'/></div>");
	$(form).append("<div id='div_input_room'><p class='formlabel'>Choisir une salle:</p><input id='input_room' name='room' type='text' autocomplete /></div>");
	
	$(form).append("<div><button value='Créer cours!' id='button_validate_new_event'>Créer cours!</button></div>");
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
	
	$(hourpickerB).spinner("widget").attr("id","spinner_pick_hour_begin");
	$(minpickerB).spinner("widget").attr("id","spinner_pick_min_begin");
	$(hourpickerE).spinner("widget").attr("id","spinner_pick_hour_end");
	$(minpickerE).spinner("widget").attr("id","spinner_pick_min_end");
	$(hourpickerB).spinner("value",BeginH);
	$(minpickerB).spinner("value",BeginM);
	$(hourpickerE).spinner("value",EndHdefault);
	$(minpickerE).spinner("value",EndMdefault);	
	
	$(divUntildate).children("input").datepicker({
		minDate: new Date(FIRST_DAY_OF_WEEK_UTC+(weekday+7)*24*60*60*1000),
		dateFormat: "dd/mm/yy",
		firstDay:1,
		beforeShowDay: $.datepicker.noWeekends,
		dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
		dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
		monthNames: ["Janvier","Février","Mars","Avril","Mai", "Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"]
	});
	
	return div;
}

/*************************************
 * ********* Modify an Event *********
 * ***********************************/
function displayFormModifyEvent(courseId,BeginH,BeginM,EndH,EndM,weekday){
	var courseToModify=$("div.course[id="+courseId+"]");
	$("#newOrModifyCourse").remove();
	
	var form=createFormModifyEvent(courseId,BeginH,BeginM,EndH,EndM);
	$(form).appendTo("body");
	
	
	
	$("#newOrModifyCourse").dialog({
		title:"Modifier cours",
		minWidth:"50%",
		minHeight:"80%",
		width:"50%",
		//height:"80%",
		top:"25%",
		left:"10%",
		resizable:false,
		modal:true
	});
	$("#button_validate_new_event").on("click",function(event){
		event.stopPropagation();
		event.preventDefault;
		//console.log("FIRST_DAY_OF_WEEK_UTC unix ="+Math.floor(FIRST_DAY_OF_WEEK_UTC/1000));
		var beginUTC=Math.floor(FIRST_DAY_OF_WEEK_UTC/1000)+(weekday*24*60*60)+($("#input_pick_hour_begin").spinner("value")*60*60);
		beginUTC += ($("#input_pick_min_begin").spinner("value")*60);
		var endUTC=Math.floor(FIRST_DAY_OF_WEEK_UTC/1000)+(weekday*24*60*60)+($("#input_pick_hour_end").spinner("value")*60*60);
		endUTC += ($("#input_pick_min_end").spinner("value")*60);
		//var param={begin: beginUTC,end: endUTC, type: $("#select_choose_type").val()};
		var param=new Object();
		param.id=courseId;
		param.action="modify_course";
		param.begin=beginUTC;
		param.end=endUTC;
		param.type=$("#select_choose_type").val();
		param.room=$("#input_room").val();
		if($("#input_name").val()!="")param.name=$("#input_name").val();
		if($("#select_choose_subject").val()>0)param.id_subject=$("#select_choose_subject").val();
		console.log("button_validate_new_event/param=..."); console.dir(param);
 		$("#newOrModifyCourse").dialog("close");

		//TODO check there is no blocking course
 		$.post("functions/courses_functions.php",
 		      param,
 			function(data)
 			{
 				console.log("create_course: data="+data+"...");
 				//console.dir(data);
				try{
					var obj=jQuery.parseJSON(data);
					$(courseToModify).remove();
					displayNewCourseElementClass(data);
					$("div.course[id="+courseId+"]").on("click",function(event){
						event.stopPropagation();
						displayFormModifyEvent($(this).attr("id"),$(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_hour"),$(this).attr("end_min"),$(this).parent().parent().attr("weekday"));			
					});
				}catch(err){
					$("body").append(data)
				}

			}
		  );
	});
	
	$("#button_delete_event").on("click",function(event){
		event.stopPropagation();
		$("#newOrModifyCourse").dialog("close");
		$.post("functions/courses_functions.php",
		       {action:'delete_course',id:courseId},
 			function(data)
 			{
				if(data!="")console.log("Erreur lors de la Suppression data="+data);
 				$("div.course[id="+courseId+"]").remove();
			}
		  );
	});

}

function createFormModifyEvent(courseId,BeginH,BeginM,EndH,EndM){
	var courseToModify=$("div.course[id="+courseId+"]");
	var div=$("<div id=newOrModifyCourse></div>"); //attributes To complete
	var form=$("<div></div>");
	//$(form).append("<input id='input_weekday' type=hidden name='weekday' value="+weekday+" />");
	var divBegin=$("<div class=timespinnersframe></div>");
	var hourpickerB=$("<input onblur='checkHourField(this)' id='input_pick_hour_begin' required >");
	var minpickerB=$("<input onblur='checkMinField(this)' id='input_pick_min_begin' required >");
	$(divBegin).append("<p class='formlabel'>Heure de début:</p>");
	$(divBegin).append(hourpickerB);
	
	$(divBegin).append(minpickerB);
	$(form).append(divBegin);
	
	var divEnd=$("<div class=timespinnersframe></div>");
// 	var EndHdefault=Math.floor(((parseInt(BeginH)*60)+parseInt(BeginM)+90)/60)
// 	var EndMdefault=Math.floor((parseInt(BeginH)*60)+parseInt(BeginM)+90)%60;
	
	var hourpickerE=$("<input onblur='checkHourField(this)' id='input_pick_hour_end' required >");
	var minpickerE=$("<input onblur='checkMinField(this)' id='input_pick_min_end' required >");
	$(divEnd).append("<p class='formlabel'>Heure de fin:</p>");
	$(divEnd).append(hourpickerE);
	$(divEnd).append(minpickerE);	
	$(form).append(divEnd);
	//console.log("ID="+$(courseToModify).attr("id_subject"));
	var divSubject=$("<select name='subject' id='select_choose_subject'></select>");
	$.get("functions/lists.php",{action:"get_lists_subjects", id_group:CURRENT_GROUP_ID,id_selected:$(courseToModify).attr("id_subject")},function(data){
		$(divSubject).append("<option value=0 >--");
		$(divSubject).append(data);
	});$("#footer-menu").on("click","#button_stop_modify",stopModification());
	$(divSubject).wrap("<div id='div_select_choose_subject'></div>");
	$(divSubject).parent().prepend("<p class='formlabel'>Changer la matière:</p>");
	$(form).append($(divSubject).parent());
	
	var selectType=$("<select name='type' id='select_choose_type'></select>");
	for(i=0;i<NUMBER_OF_COURSES_TYPES;i++){
		if(i==$(courseToModify).attr("type"))$(selectType).append("<option value="+i+" selected >"+getTypeName(i));
		else $(selectType).append("<option value="+i+" >"+getTypeName(i));
	}
	$(selectType).wrap("<div id='div_select_choose_type'></div>");
	$(selectType).parent().prepend("<p class='formlabel'>Type de cours:</p>");
	$(form).append($(selectType).parent());
	
	var input_name=$("<input id='input_name' name='name' type='text'/>");
	if($(courseToModify).children("p[name]").get(0)!==undefined){
		$(input_name).val($(courseToModify).children("p[name]").text());
	}
	var div_input_name=$("<div id='div_input_name'><p class='formlabel'>Intitulé Supplémentaire:</p></div>");
	$(div_input_name).append(input_name);
	//$(form).append("<div id='div_input_name'><p class='formlabel'>Intitulé Supplémentaire:</p><input id='input_name' name='name' type='text'/></div>");
	$(form).append(div_input_name);
	
	var input_room=$("<input id='input_room' name='room' type='text' autocomplete/>");
	if($(courseToModify).children("p[room]").get(0)!==undefined){
		$(input_room).val($(courseToModify).children("p[room]").text());
	}
	var div_input_room=$("<div id='div_input_room'><p class='formlabel'>Changer la salle:</p></div>");
	$(div_input_room).append(input_room);
	//$(form).append("<div id='div_input_room'><p class='formlabel'>Changer la salle:</p><input id='input_room' name='room' type='text' autocomplete /></div>");
	$(form).append(div_input_room);
	
	$(form).append("<div><button value='Valider!' id='button_validate_new_event'>Valider!</button><button  id='button_delete_event'>Supprimer!</button></div>");
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
	if(EndH==HOUR_MIN)$(minpickerB).spinner("option","min",(BEGIN_MIN+TIME_INTERVAL_IN_MIN)%60);
 	if(EndH==HOUR_MAX)$(minpickerB).spinner("option","max",END_MIN);
	
	$(hourpickerB).spinner("widget").attr("id","spinner_pick_hour_begin");
	$(minpickerB).spinner("widget").attr("id","spinner_pick_min_begin");
	$(hourpickerE).spinner("widget").attr("id","spinner_pick_hour_end");
	$(minpickerE).spinner("widget").attr("id","spinner_pick_min_end");
	$(hourpickerB).spinner("value",BeginH);
	$(minpickerB).spinner("value",BeginM);
	$(hourpickerE).spinner("value",EndH);
	$(minpickerE).spinner("value",EndM);	
	
	
	
	return div;
}
/***********************************/


function loadTimetableForWeek(idTimetable,firstweekdayutc){
	$("button.hide_row").remove();
	$("tr.calendar").show();
	var param=new Object();
	param.begin=Math.floor(firstweekdayutc/1000);
	param.end=param.begin+(5*24*60*60);
	param.id=idTimetable;
	param.action='load_courses_between';
	$.post("functions/courses_functions.php",
		      param,
			function(list)
			{
				//console.log("loadTimetableForWeek/list="+list+"...");
				try{
					list=jQuery.parseJSON(list);
					for(i=0;i<list.length;i++)
					{
						displayNewCourseElementClass(list[i]);
					}
					
					adaptDaysOfWeekDate(firstweekdayutc);
					var date_1=new Date(firstweekdayutc);
					var date_2=new Date(firstweekdayutc+4*24*60*60*1000);
					$("#week_dates").html("Semaine du "+date_1.getDate()+"/"+(date_1.getMonth()+1)+" au "+date_2.getDate()+"/"+(date_2.getMonth()+1));
				
					if($("#button_stop_modify").get(0)!=undefined){
						$("div.course").on("click",function(event){
							event.stopPropagation();
							displayFormModifyEvent($(this).attr("id"),$(this).attr("begin_hour"),$(this).attr("begin_min"),$(this).attr("end_hour"),$(this).attr("end_min"),$(this).parent().parent().attr("weekday"));			
						});
					}
					
				}catch(err){
					$("body").append(list);
				}

			}
	  );
	
		
}

function adaptDaysOfWeekDate(firstweekdayutc){
	var day_names=["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"];
	//var date=new Date(firstweekdayutc);
// 	$("td.datesrow div").html(day_names[$(this).parent().attr("weekday")]
// 		+" "+new Date(firstweekdayutc+($(this).parent().attr("weekday")*24*60*60*1000)).getDate()
// 		+"/"+(new Date(firstweekdayutc+($(this).parent().attr("weekday")*24*60*60*1000)).getMonth()+1));
	for(i=0;i<5;i++){
			$("td.datesrow[weekday="+i+"] div").text(day_names[i]
				+" "+new Date(firstweekdayutc+(i*24*60*60*1000)).getDate()
				+"/"+(new Date(firstweekdayutc+(i*24*60*60*1000)).getMonth()+1));
	}
	//$("td.datesrow").text($(this).attr("weekday"));
	//console.log($("td.datesrow div").parent().attr("weekday"));
	//$("td.datesrow div").text(day_names[parseInt($(this).attr("weekday"))]);
	//$("td.datesrow div").text($(this).attr("weekday"));
}

function checkHourField(field){
	$("#button_validate_new_event").removeAttr("disabled");
	for(i=0;i<field.value.length;i++){
			if(isNaN(parseInt(field.value.charAt(i)))){
				//field.style.backgroundColor = "#FF0000";
				field.value="";
				$("#button_validate_new_event").attr("disabled","disabled");
				return;
			}
	}
	var time = parseInt(field.value);
	if( isNaN(time) ||(time > 23) || (time <0) ) {
		//field.style.backgroundColor = "#FF0000";
		//field.value="";
		$("#button_validate_new_event").attr("disabled","disabled");
	}
	if($("#input_pick_hour_begin").val()!="" && $("#input_pick_min_begin").val()!=""
			&& $("#input_pick_hour_end").val()!="" && $("#input_pick_min_end").val()!="")
	{
		if($("#input_pick_hour_end").spinner("value")>$("#input_pick_hour_begin").spinner("value")){
			$("#button_validate_new_event").removeAttr("disabled");
		}
		if($("#input_pick_hour_end").spinner("value")==$("#input_pick_hour_begin").spinner("value")
			&& ($("#input_pick_min_end").spinner("value")>$("#input_pick_min_begin").spinner("value"))){
			$("#button_validate_new_event").removeAttr("disabled");
		}
			
	}
	else $("#button_validate_new_event").attr("disabled","disabled");
				
	//else {
		//field.style.backgroundColor = "#fff";
		//$("#button_validate_new_event").removeAttr("disabled");
	//}
}

function checkMinField(field){
	$("#button_validate_new_event").removeAttr("disabled");
	for(i=0;i<field.value.length;i++){
			if(isNaN(parseInt(field.value.charAt(i)))){
				//field.style.backgroundColor = "#FF0000";
				field.value="";
				$("#button_validate_new_event").attr("disabled","disabled");
				return;
			}
	}
	var time = parseInt(field.value);
	if( isNaN(time) ||(time > 59) || (time <0) || (time%TIME_INTERVAL_IN_MIN) != 0) {
		//field.style.backgroundColor = "#FF0000";
		field.value="";
		$("#button_validate_new_event").attr("disabled","disabled");
	}
	if($("#input_pick_hour_begin").val()!="" && $("#input_pick_min_begin").val()!=""
			&& $("#input_pick_hour_end").val()!="" && $("#input_pick_min_end").val()!="")
	{
		if($("#input_pick_hour_end").spinner("value")>$("#input_pick_hour_begin").spinner("value")){
			$("#button_validate_new_event").removeAttr("disabled");
		}
		else if($("#input_pick_hour_end").spinner("value")==$("#input_pick_hour_begin").spinner("value")
			&& ($("#input_pick_min_end").spinner("value")>$("#input_pick_min_begin").spinner("value"))){
			$("#button_validate_new_event").removeAttr("disabled");
		}
		
			
	}
	else $("#button_validate_new_event").attr("disabled","disabled");
	
// 	if((time%TIME_INTERVAL_IN_MIN) != 0) {
// 		field.style.backgroundColor = "#FF0000";
// 	}
// 	else {
// 		field.style.backgroundColor = "#fff";
// 	}
}

function stopModification(){
		//TODO complete pour informer le serveur
	$("div.course").off("click");
	$("td.daycolumn>div").off("click");
	$("div.course,td.daycolumn>div").on("click",function(){
		alert("Vous n'avez pas les droits pour modifier\nou vous n'avez pas activé la modification");
	});
	
	if($("#button_stop_modify").get(0)!=undefined){
		$("#button_stop_modify").after("<a class='btn col-md-1'  id='button_modify_timetable' ><i class='fa fa-edit'></i> Modifier</a><a class='btn col-md-1' id='button_validate_timetable'><i class='fa fa-check-square-o'></i> Valider</a>");
		$("#button_stop_modify").remove();
	}
	
}


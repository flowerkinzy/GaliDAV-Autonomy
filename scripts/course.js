var NUMBER_OF_COURSES_TYPES=6;

function getTypeName(type){
	
	switch(type){
		case 0:
			return "CM";
		case 1:
			return "TD";
		case 2:
			return "TP";
		case 3:
			return "CONFERENCE";
		case 4:
			return "EXAMEN";
		case 5:
			return "RATTRAPAGE";
		default:
			return "";
	}
}

function createNewCourseElementClass(Course) { 

	var beginDate=new Date(Course.time_begin*1000);
	var endDate=new Date(Course.time_end*1000);
	var durationInMin=Math.floor((Course.time_end-Course.time_begin)/60);
	console.log("TOP:tr.calendar[begin_hour="+beginDate.getHours()
		+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)
		+"]");
	var top=$("tr.calendar[begin_hour="+beginDate.getHours()
		+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)
		+"]").offset().top;
	console.log("BOTTOM:tr.calendar[begin_hour="+endDate.getHours()
		+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN)
		+"]");
		var endmin=TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN);
		
		if(endmin==0){
			var bottom=$("tr.calendar[begin_hour="+(endDate.getHours()-1)
				+"][end_min=60]").offset().top+$("tr.calendar[begin_hour="+(endDate.getHours()-1)
				+"][end_min=60]").height();
		}else{
			var bottom=$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN)
				+"]").offset().top+$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN)
				+"]").height();
		}
	var height=bottom-top;
	var width=$($("td.daycolumn")[0]).width();
 	
	var res="<div class='course fullspace-x' style='background-color:white;z-index:2;position:absolute;min-height:"+
			height+"px; max-height:"+height+"px; "+
			"max-width:"+width+"px; width:"+width+"px;min-width:"+Math.floor(width/3)+"px'"+
			" begin_hour="+beginDate.getHours()+
			" begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)+
			" end_hour="+endDate.getHours()+
			" end_min="+TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN)+
			" weekday="+(beginDate.getDay()-1)%7+
			" id="+
			Course.sqlId+"><p><b>"+
			Course.subject_name;
			
			if(getTypeName(Course.courseType)!="")
				res=res+" "+getTypeName(Course.courseType);
			if(typeof Course.room !== 'undefined')
				res=res+"</b></p><p><i>"+Course.room+"</i></p></div>";
			else
				res=res+"</b></p></div>";
		
			return res;

        
}

//Note: par défaut, les cours concernent tous les élèves affiliés à lEDT. Il faudra préciser un évènement de groupe pour
//avoir 2+ cours sur la même plage horaires. (Et donc empêcher le cours de prendre toute la place);
function displayNewCourseElementClass(Course) {
	console.log("displayNewCourseElementClass/@param="+Course);	
	Course=jQuery.parseJSON(Course);
	var beginDate=new Date(Course.time_begin*1000);
	var endDate=new Date(Course.time_end*1000);
	var weekday=beginDate.getDay(); //0 is for Sunday and so on
	var beginM=TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN);
	var beginH=beginDate.getHours();
	var endM=TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN);
	var endH=endDate.getHours();
	if(weekday<1 || weekday>5);
	else if(beginH < HOUR_MIN || beginH> HOUR_MAX);
	else if(endH < HOUR_MIN || endH> HOUR_MAX);
	else if(beginH==HOUR_MIN && beginM < BEGIN_MIN);
	else if(beginH==HOUR_MAX && beginM >= END_MIN);
	else if(endH==HOUR_MIN && endM <= BEGIN_MIN);
	else if(endH==HOUR_MAX && endM > END_MIN);
	else{
		$("td.daycolumn[begin_hour="
			+beginH+"][begin_min="+beginM+"][weekday="+(weekday-1)%7+"]>div")
				.append(createNewCourseElementClass(Course));
		}
}


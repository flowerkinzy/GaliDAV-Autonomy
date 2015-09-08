var NUMBER_OF_COURSES_TYPES=7;

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
		case 6:
			return "REUNION";
		default:
			return "";
	}
}

function createNewCourseElementClass(Course) { 

	var beginDate=new Date(Course.time_begin*1000);
	var endDate=new Date(Course.time_end*1000);
	var durationInMin=Math.floor((Course.time_end-Course.time_begin)/60);
// 	console.log("TOP:tr.calendar[begin_hour="+beginDate.getHours()
// 		+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)
// 		+"]");
	var top=$("tr.calendar[begin_hour="+beginDate.getHours()
		+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)
		+"]").offset().top;
// 	console.log("BOTTOM:tr.calendar[begin_hour="+endDate.getHours()
// 		+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN)
// 		+"]");
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
		
	//TODO check if there is another course!!!
	/**
	 * 
	 * 
	 * Then change the width of the already present course,+ adpt this course width
	 * 
	 */
	var containingDiv=$("td.daycolumn[begin_hour="+
			beginDate.getHours()+"][begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)
			+"][weekday="+(beginDate.getDay()-1)%7+"]>div");
	if($(containingDiv).html()!=undefined)console.log("content of containingDiv="+$(containingDiv).html());
	var numberOfCourses;
 	if($(containingDiv).children()==undefined)numberOfCourses=0;
	else numberOfCourses=$(containingDiv).children().length;
		console.log("numberOfCourses="+numberOfCourses);
// 	//if(numberOfCourses==0){
		var height=bottom-top;
		var width=$($("td.daycolumn")[0]).width()-1;
		var margin_left=0;
// 	//}
	if(numberOfCourses==1){
		
		height=bottom-top;
		
		width=Math.floor($($("td.daycolumn")[0]).width()/2)-1;
		margin_left=width+1;
		$(containingDiv).children().css("width",width+"px");
		$(containingDiv).children().css("max-width",width+"px");
		$(containingDiv).children().css("margin-left",margin_left+"px");
	}
	if(numberOfCourses==2){
		
		height=bottom-top;
		
		width=Math.floor($($("td.daycolumn")[0]).width()/3)-1;
		margin_left=width;
		$(containingDiv).children().css("width",width+"px");
		$(containingDiv).children().css("max-width",width+"px");
		$(containingDiv).children().get(0).css("margin-left",margin_left+"px");
		$(containingDiv).children().get(1).css("margin-left",(margin_left*2)+"px");
	}
		
	/**
	 ******************/
	
 	
	var res="<div "
	if(height<=40)res =res+"class='course fullspace-x short-course'";
	else res =res+"class='course fullspace-x'";
	if(getTypeName(Course.courseType)=="EXAMEN" || getTypeName(Course.courseType)=="RATTRAPAGE")res = res + "style='background-color:red;";
	else if(getTypeName(Course.courseType)=="REUNION")res = res + "style='background-color:orange;";
	else if(Course.subject==0 || Course.subject==undefined)res = res + "style='background-color:white;";
	else res = res + "style='background-color:"+getColorFromId(Course.subject)+";";
	res = res + "z-index:2;position:absolute;min-height:"+
			height+"px; max-height:"+height+"px; height:"+height+"px; "+
			"max-width:"+width+"px; width:"+width+"px;min-width:"+Math.floor(width/3)+"px'"+
			" begin_hour="+beginDate.getHours()+
			" begin_min="+TIME_INTERVAL_IN_MIN*Math.floor(beginDate.getMinutes()/TIME_INTERVAL_IN_MIN)+
			" end_hour="+endDate.getHours()+
			" end_min="+TIME_INTERVAL_IN_MIN*Math.floor(endDate.getMinutes()/TIME_INTERVAL_IN_MIN)+
			" weekday="+(beginDate.getDay()-1)%7+
			" id="+
			Course.sqlId+" type="+Course.courseType+
			" id_group="+Course.id_group;
			if(Course.subject==0 || Course.subject==undefined)res=res+" id_subject=0 ><p>";
			else res=res+" id_subject="+Course.subject+" ><p>";
			if(Course.subject_name!= undefined)res =res + Course.subject_name;
			
			
			if(getTypeName(Course.courseType)!="")
				res=res+" "+getTypeName(Course.courseType);
			if(Course.name!= undefined)res = res+"</p><p name>"+Course.name;
			if(typeof Course.room !== 'undefined')
				res=res+"</p><p room>"+Course.room+"</p></div>";
			else
				res=res+"</p></div>";
		
			return res;

        
}

//Note: par défaut, les cours concernent tous les élèves affiliés à lEDT. Il faudra préciser un évènement de groupe pour
//avoir 2+ cours sur la même plage horaires. (Et donc empêcher le cours de prendre toute la place);
function displayNewCourseElementClass(Course) {
	//console.log("displayNewCourseElementClass/@param="+Course);	
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

function getColorFromId(id){
	var range=255;
	var min=70;
	var max=220;
	var R=min+Math.floor((max*id)%(range-min));
	var G=min+Math.floor(R*id)%(range-min);
	var B=min+Math.floor((R*range)+(G*id))%(max-min);
	var result="#"+R.toString(16)+G.toString(16)+B.toString(16);
	return result;
}
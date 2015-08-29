var hourmin=8;
var hourmax=19;
var beginmin=30;
var endmin=45;
var timeintervalinmin=15;
var timeintervaldisplayinmin=30;

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
	console.log("createNewCourseElementClass...");
	var beginDate=new Date(Course.begin*1000);
	var endDate=new Date(Course.end*1000);
	var durationInMin=Math.floor((Course.end-Course.begin)/60);
	console.log("TOP:tr.calendar[begin_hour="+beginDate.getHours()
		+"][begin_min="+timeintervalinmin*Math.floor(beginDate.getMinutes()/timeintervalinmin)
		+"]");
	var top=$("tr.calendar[begin_hour="+beginDate.getHours()
		+"][begin_min="+timeintervalinmin*Math.floor(beginDate.getMinutes()/timeintervalinmin)
		+"]").offset().top;
	console.log("BOTTOM:tr.calendar[begin_hour="+endDate.getHours()
		+"][begin_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
		+"]");
		var endmin=timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin);
		
		if(endmin==0){
			var bottom=$("tr.calendar[begin_hour="+(endDate.getHours()-1)
				+"][end_min=60]").offset().top+$("tr.calendar[begin_hour="+(endDate.getHours()-1)
				+"][end_min=60]").height();
		}else{
			var bottom=$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
				+"]").offset().top+$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
				+"]").height();
		}
	var height=bottom-top;
	var width=$($("td.daycolumn")[0]).width();
        var res="<div class='course fullspace-x' style='background-color:white;z-index:2;position:absolute;min-height:"+
			height+"px; max-height:"+height+"px; "+
			"max-width:"+width+"px; width:"+width+"px;min-width:"+Math.floor(width/3)+"px'"+
			" begin_hour="+beginDate.getHours()+
			" begin_min="+timeintervalinmin*Math.floor(beginDate.getMinutes()/timeintervalinmin)+
			" end_hour="+endDate.getHours()+
			" end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)+
			" weekday="+(beginDate.getDay()-1)%7+
			" id="+
			Course.sqlId+"><p><b>"+
			Course.subject.name;
			if(getTypeName(Course.coursesType)!="")
				res=res+" T:"+getTypeName(Course.coursesType);
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
	var beginDate=new Date(Course.begin*1000);
	var endDate=new Date(Course.end*1000);
	var weekday=beginDate.getDay(); //0 is for Sunday and so on
	var beginM=timeintervalinmin*Math.floor(beginDate.getMinutes()/timeintervalinmin);
	var beginH=beginDate.getHours();
	var endM=timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin);
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


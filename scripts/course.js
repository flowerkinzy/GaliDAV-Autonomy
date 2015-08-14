
var timeintervalinmin=15;	

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
		+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
		+"]");
		var endmin=timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin);
		if(endmin==60){
			var bottom=$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
				+"]").offset().top+$("tr.calendar[begin_hour="+endDate.getHours()-1
				+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
				+"]").height();
		}else{
			var bottom=$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
				+"]").offset().top+$("tr.calendar[begin_hour="+endDate.getHours()
				+"][end_min="+timeintervalinmin*Math.floor(endDate.getMinutes()/timeintervalinmin)
				+"]").height();
		}
	var height=bottom-top;
	console.dir($("td.daycolumn")[0]);
	var width=$($("td.daycolumn")[0]).width();
	console.log("width="+width);
        var res="<div class='course fullspace-x' style='background-color:white;z-index:2;position:absolute;min-height:"+
			height+"px; max-height:"+height+"px; "+
			"max-width:"+width+"px; width:"+width+"px;min-width:"+Math.floor(width/3)+"px'"+
			" begin_hour="+beginDate.getHours()+
			" begin_min="+beginDate.getMinutes()+
			" end_hour="+endDate.getHours()+
			" end_min="+endDate.getMinutes()+
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


function displayNewCourseElementClass(Course) {
	console.log("displayNewCourseElementClass/@param="+Course);	
	Course=jQuery.parseJSON(Course);
	var beginDate=new Date(Course.begin*1000);
	var endDate=new Date(Course.end*1000);
	var weekday=beginDate.getDay(); //0 is for Sunday and so on
	console.log(beginDate.toUTCString()+" -> "+endDate.toUTCString());
	console.log("weekday="+weekday);
    $("td.daycolumn[begin_hour="
		+beginDate.getHours()+
		"][begin_min="
		+timeintervalinmin*Math.floor(beginDate.getMinutes()/timeintervalinmin)
		+"][weekday="+
		(weekday-1)%7+"]>div")
		.append(createNewCourseElementClass(Course));
}


var hourmin=8;
var hourmax=19;
var timeintervalinmin=15;
var timeintervaldisplayinmin=30;
 $( document ).ready(function() {
	$("tr.calendar").on("click","td.hourcolumn",function (){
			$("#wrap").append("<button style='padding:0;position:absolute;cursor:default' begin_hour=10...> \> </button>");
			
			$( "button" ).position({
				my: "right",
				at: "left top",
				of: "tr.calendar[begin_hour=10][begin_min=0]"
			});
			$(this).parent().children().children().slideUp(1000);
	});

	
//FIN documentReady
	});
	
	
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
	console.dir(Course);
	console.log("begin="+Course.begin);
	console.log("end="+Course.end);
	var durationInMin=Math.floor((Course.end-Course.begin)/60);
	console.log("tr.calendar height="+$("tr.calendar").height());
	console.log("duration="+durationInMin);
	var height=$("tr.calendar").height()*durationInMin/timeintervalinmin;
	
        var res="<div class='courses' style='background-color:white;z-index:2;position:absolute;min-height:"+
			height+"px; ' id="+
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
	console.log(beginDate.toGMTString()+" -> "+endDate.toGMTString());
	console.log("weekday="+weekday);
    $("td.daycolumn[begin_hour="
		+beginDate.getHours()+
		"][begin_min="
		+Math.floor(beginDate.getMinutes()/60)
		+"][weekday="+
		(weekday-1)%7+"]>div")
		.append(createNewCourseElementClass(Course));
}

function test(){
	console.log("test");
		$("body").css("background-color","red");
		$("body").attr("attr",1);
}
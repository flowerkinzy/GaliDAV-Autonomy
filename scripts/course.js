
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
        var res="<div class='courses' style='background-color:white;z-index:2;position:absolute;' id="+
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
    $("td.daycolumn[begin_hour="
		+beginDate.getHours()+
		"][begin_min="
		+Math.floor(beginDate.getMinutes()/60)
		+"]>div")
		.append(createNewCourseElementClass(Course));
}

function test(){
	console.log("test");
		$("body").css("background-color","red");
		$("body").attr("attr",1);
}
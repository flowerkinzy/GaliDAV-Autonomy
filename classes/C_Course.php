<?php
/**
 * \file    C_Course.php
 * \brief   Defines the class Course.
 * \details Represents a course. A course in GaliDAV database doesn’t correspond to a collection item (event) in DAViCal database.
	In fact, a collection item has repetition rules that could be translated in several courses in GaliDAV database.
	On the other hand, a GaliDAV course can be shared by.
*/
error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5'))
{
	die('This file was written for PHP 5');
}
include_once("functions/error_handling.php");
require_once('classes/C_Database.php');
require_once('types/T_Courses.php');
require_once('classes/C_Timetable.php');
require_once('classes/C_Subject.php');

class Course
{
	// --- ATTRIBUTES ---
	private $sqlId       = NULL;
	private $number      = NULL;
	private $time_begin	=	NULL;
	private $time_end	=	NULL;
	private $room        = NULL;
	private $courseType = NULL;
	private $subject     = NULL;
	private $name	=	NULL;
	private $id_group	=	NULL;

	const TABLENAME           = "gcourse";
	const SQLcolumns          = "id serial PRIMARY KEY, name VARCHAR(30), room VARCHAR(30), begins_at TIMESTAMP WITHOUT TIME ZONE NOT NULL, ends_at TIMESTAMP WITHOUT TIME ZONE NOT NULL, id_subject INTEGER REFERENCES gsubject(id), type INTEGER, number INTEGER, id_original_group INTEGER REFERENCES ggroup(id), creation_timestamp TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL";
	const belongsToTABLENAME  = "gcourse_belongs_to";
	const belongsToSQLcolumns = "id_course INTEGER REFERENCES gcourse(id), id_calendar INTEGER REFERENCES gcalendar(id), CONSTRAINT gcourse_belongs_to_pk PRIMARY KEY(id_course, id_calendar)";

	/* TODO : Make the links with davical and name davical events differently depending on the collection (timetable) they belong to
	*/

	// --- OPERATIONS ---write in DataBase
	/**
	 * \brief Course’s constructor
	 * \param $newSubject The subject of the course.
	 * \param $newBegin   ???
	 * \param $newEnd     ???
	*/
	public function __construct($newSubject=NULL, $newBegin=NULL, $newEnd=NULL,$optionalGroup=NULL)
	{
		
		if(is_int($newBegin) && is_int($newEnd)){
			if($newBegin>=$newEnd)echo "<script>console.log('Horaires de cours non conforme');</script>";
			else{
				if($optionalGroup instanceof Group)$optionalGroup=$optionalGroup->getSqlId();
				if($newSubject instanceof Subject)$newSubject=$newSubject->getSqlId();
				if(is_int($newSubject))
					$query="INSERT INTO " . self::TABLENAME . " (begins_at, ends_at,id_subject) VALUES ($1, $2,$3);";
				else
					$query="INSERT INTO " . self::TABLENAME . " (begins_at, ends_at) VALUES ($1, $2);";
				$params[]        = date('Y-m-d G:i:s',$newBegin);
				$params[]        = date('Y-m-d G:i:s',$newEnd);
				if(is_int($newSubject))$params[]        = $newSubject;
				$result          = Database::currentDB()->executeQuery($query, $params);
				if (!$result)
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
				
				else{
					$params = [];
					$query  = "SELECT id FROM " . self::TABLENAME . " ORDER BY creation_timestamp DESC, id DESC;";
					$result = Database::currentDB()->executeQuery($query, $params);

					if (!$result)
					{
						Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);

					}
					else
					{
						
						
						$result=pg_fetch_assoc($result);
						if(is_int($newSubject))$this->subject = $newSubject;
						$this->time_begin   = $newBegin;
						$this->time_end    = $newEnd;
 						$this->sqlId     = intval($result['id']);
						if(is_int($newSubject)|| is_int($optionalGroup)){
							$S=new Subject();
							if(is_int($newSubject) && $newSubject>0)$S->loadFromDB($newSubject);
							
							$T=new Timetable();
							
							if(is_int($S->getSqlId())){
								$T->loadFromDB($S->getTimetable());
								$T->addCourse($this);
							}
							$G=new Group();
							if(is_int($S->getSqlId()))$G->loadFromDB($S->getGroup());
							else {
								//echo("<pre>optionalGroup used</pre>");
								$G->loadFromDB($optionalGroup);
							}
							
							$this->id_group=$G->getSqlId();
							$query="UPDATE " . self::TABLENAME . " SET id_original_group=".$this->id_group." where id=".$this->sqlId.";";
							if(!Database::currentDB()->executeQuery($query)){
								Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
							}
							
							//echo("<h1>group</h1>".+print_r($G));
							$T=new Timetable();
							$T->loadFromDB($G->getTimetable());
							$T->addCourse($this);
							
							//TODO add to all depending groups' calendars
							$list=$G->getDependingGroupsList();
							foreach($list as $groupid){
								$G2=new Group();
								$G2->loadFromDB($groupid);
								$T2=new Timetable();
								$T2->loadFromDB($G2->getTimetable());
								$T2->addCourse($this);
							
							}
							
							if(is_int($S->getSqlId())){
								foreach ($S->getTeachedByList() as $idSpeaker) // for all speakers of this course
								{
									$aSpeaker=new Teacher();
									$aSpeaker->loadFromDB($idSpeaker);
									if ($aSpeaker->hasStatus(new PersonStatus(PersonStatus::SPEAKER))){
										
										$T=new Timetable();
										$T->loadFromDB($aSpeaker->getTimetable());
										$T->addCourse($newCourse);
									}

								}
							}
						
						}
						
					
					}
				}
				
			}	
		}
	}
	

	// getters
	/**
	 * \brief  Getter for the attribute $number.
	 * \return The \e integer value of $number.
	*/
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * \brief  Getter for the attribute $begin.
	 * \return The value of $begin.
	*/
	public function getBegin()
	{
		return $this->time_begin;
	}

	/**
	 * \brief  Getter for the attribute $begin.
	 * \return The string value of $begin.
	*/
	public function getBeginString()
	{
		return date('d/m/Y H:i', $this->time_begin);
	}

	/**
	 * \brief  Getter for the attribute $end.
	 * \return The value of $end.
	*/
	public function getEnd()
	{
		return $this->time_end;
	}

	/**
	 * \brief  Getter for the attribute $end.
	 * \return The string value of $end.
	*/
	public function getEndString()
	{
		return date('d/m/Y H:i', $this->time_end);
	}

	/**
	 * \brief  Getter for the attribute $room.
	 * \return The string value of $room.
	*/
	public function getRoom()
	{
		return $this->room;
	}

	/**
	 * \brief  Getter for the attribute $courseType.
	 * \return The value of $courseType.
	*/
	public function getCourseType()
	{
		return $this->courseType;
	}

	/**
	 * \brief  Getter for the attribute $courseType.
	 * \return The string value of $courseType.
	*/
	public function getCourseTypeString()
	{
		switch ($this->courseType)
		{
			case (CM):
				return "CM";
			case (TD):
				return "TD";
			case (TP):
				return "TP";
			case (EXAMEN):
				return "Partiel";
			case (CONFERENCE):
				return "Conférence";
			case (RATTRAPAGE):
				return "Rattrapage";
			case (REUNION):
				return "Réunion";
			default:
				return "Type inconnu";
		}
	}

	/**
	 * \brief  Getter for the attribute $subject.
	 * \return The value of $subject.
	*/
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * \brief  Getter for the attribute $sqlId.
	 * \return The integer value of $sqlId.
	*/
	public function getSqlId()
	{
		return $this->sqlId;
	}

	// setters
	/**
	 * \brief  Setter for the attribute $number.
	 * \param  $newNumber Contains the new value of $number.
	*/
	protected function setNumber($newNumber)
	{
		if (is_int($newNumber))
		{
			$query  = "UPDATE " . self::TABLENAME . " SET number = $1 WHERE id = " . $this->sqlId . ";";
			$params = array($newNumber);

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->number = $newNumber;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}
	
	public function setName($name)
	{
		if (is_string($name))
		{
			$query  = "UPDATE " . self::TABLENAME . " SET name = $1 WHERE id = " . $this->sqlId . ";";
			$params = array($name);

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->name = $name;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	/**
	 * \brief  Setter for the attribute $begin.
	 * \param  $newBegin Contains the new value of $begin.
	*/
	public function setBegin($newBegin)
	{
		
		if (is_int($newBegin))
		{
			$query  = "UPDATE " . self::TABLENAME . " SET begins_at = $1 WHERE id = " . $this->sqlId . ";";
			$params = array(date('Y-m-d G:i:s',$newBegin));

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->time_begin = $newBegin;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
			
		}
	}

	/**
	 * \brief  Setter for the attribute $end.
	 * \param  $newEnd Contains the new value of $end.
	*/
	public function setEnd($newEnd)
	{
		if (is_int($newEnd))
		{
			$query  = "UPDATE " . self::TABLENAME . " SET ends_at = $1 WHERE id = " . $this->sqlId . ";";
			$params = array(date('Y-m-d G:i:s',$newEnd));

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->time_end = $newEnd;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	/**
	 * \brief  Setter for the attribute $room.
	 * \param  $newRoom Contains the new value of $room.
	*/
	public function setRoom($newRoom)
	{
		if (is_string($newRoom))
		{
			$query  = "UPDATE " . self::TABLENAME . " SET room = $1 WHERE id = " . $this->sqlId . ";";
			$params = array($newRoom);

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->room = $newRoom;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
			
		}
	}

	/**
	 * \brief  Setter for the attribute $courseType.
	 * \param  $newCoursesType Contains the new value of $courseType.
	*/
	public function setCourseType($newCoursesType)
	{
		if (is_int($newCoursesType))
		{
			
			$query  = "UPDATE " . self::TABLENAME . " SET type = $1 WHERE id = " . $this->sqlId . ";";
			$params = array($newCoursesType);

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->courseType = $newCoursesType;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	/**
	 * \brief  Setter for the attribute $subject.
	 * \param  $newSubject Contains the new value of $subject.
	*/
	public function setSubject($newSubject=NULL)
	{
		if ($newSubject!=NULL)
		{
			if($newSubject instanceof Subject)$newSubject=$newSubject->getSqlId();
			if(is_int($newSubject)){
				$query  = "UPDATE " . self::TABLENAME . " SET id_subject = $1 WHERE id = " . $this->sqlId . ";";
				$params = array($newSubject);

				if (Database::currentDB()->executeQuery($query, $params))
				{
					$this->subject=$newSubject;
				}
				else
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
			}
		}else{
			$query  = "UPDATE " . self::TABLENAME . " SET id_subject =NULL WHERE id = " . $this->sqlId . ";";

			if (Database::currentDB()->executeQuery($query))
			{
				$this->subject=$newSubject;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
		//TODO adapt all calendars it should be intergrated to
	}

	// others
	/**
	 * \brief Integrates this course into the given timetable.
	 * \param $aTimetable The timetable in which the course will be integrated.
	*/
	//DEPRECATED
	public function integrateInTimetable(Timetable $aTimetable)
	{
		$aTimetable->addCourse($this);
	}

	
	/**
	 * \brief  Loads data from the database.
	 * \param  $id The SQL id of the timetable to load.
	 * \param  $onlyClassCalendar \e Boolean ???
	 * \return TRUE if data loaded successfully, FALSE otherwise.
	*/
	public function loadFromDB($id = NULL)
	{
		
		if ($id == NULL) // if we do not want to load a particular course
		{
			if (is_int($this->sqlId)) // check if the current course object is defined
			{
				$id = $this->sqlId; // if yes, we want to “reload” data about this object from the database (UPDATE)
			}
		}

		if ($id == NULL) // if no, the first course object of the DB, will be chosen to be loaded
		{
			$query = "SELECT * FROM " . self::TABLENAME . ";";
			$result = Database::currentDB()->executeQuery($query);
		}
		else // (if yes) from here, we load data about the course that has $id as sqlId
		{
			$query = "SELECT * FROM " . self::TABLENAME . " WHERE id = $1;";
			$params = array($id);
			$result = Database::currentDB()->executeQuery($query, $params);
		}

		if ($result)
		{
			$ressource = pg_fetch_assoc($result); // ressource is now an array containing values for each SQLcolumn of the timetable table
			$this->loadFromRessource($ressource);
			return TRUE;
		}
		else Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
		return FALSE;
	}

	/**
	 * \brief Loads all data from the given ressource.
	 * \param $ressource The ressource from which data will be loaded.
	*/
	public function loadFromRessource($ressource)
	{
		// we change values of attributes
		
		$this->sqlId = intval($ressource['id']);
		$this->number=NULL;
		if($ressource['number']){
			$this->number=intval($ressource['number']);
		}
		$this->time_begin=0;
		if($ressource['begins_at']){
			$this->time_begin=strtotime($ressource['begins_at']);
		}
		$this->time_end=0;
		if($ressource['ends_at']){
			$this->time_end=strtotime($ressource['ends_at']);
		}
		$this->room=NULL;
		if($ressource['room']){
			$this->room=$ressource['room'];
		}
		$this->courseType=0;
		if($ressource['type']){
			$this->courseType=intval($ressource['type']);
		}
		$this->subject=NULL;
		if($ressource['id_subject']!=NULL){
			$this->subject=intval($ressource['id_subject']);
		}
		$this->name=NULL;
		if($ressource['name']!=NULL){
			$this->name=$ressource['name'];
		}
		$this->id_group=NULL;
		if($ressource['id_original_group']!=NULL){
			$this->id_group=intval($ressource['id_original_group']);
		}
		
	}

	
	/**
	 * \brief Removes the course from database.
	*/
	public function removeFromDB()
	{
		$query="DELETE FROM ".self::belongsToTABLENAME." WHERE id_course=".$this->sqlId.";";
		if (!Database::currentDB()->executeQuery($query))
		{
			Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
		}
		$query="DELETE FROM ".self::TABLENAME." WHERE id=".$this->sqlId.";";
		if (!Database::currentDB()->executeQuery($query))
		{
			Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
		}
	}

	/**
	 * \brief  Converts data about the course in HTML format.
	 * \return \e String containing data about the course.
	*/
	public function toHTML()
	{
		$result = "<p>Matière:&emsp; &emsp; " . $this->subject->getName() . "<br/>Type de cours:&emsp; &emsp;" . $this->courseType . "&emsp; &emsp;&emsp; &emsp;Numero:&emsp; &emsp; " . $this->number . "<br/>Horaires:&emsp; du " . $this->getBeginString() . " au " . $this->getEndString() . "<br/>Salle: &emsp; &emsp; " . $this->room . "</p>";

		return $result;
	}
	
	public function to_array(){
		$result=array();
		foreach($this as $key => $value) {
			if(!is_null($value)){
				if(!is_object($value))$result[$key] = $value;
				else {
					if(method_exists($value,"getSqlId"))$result[$key."_id"]=$value->getSqlId();
					if(method_exists($value,"getName"))$result[$key."_name"]=$value->getName();
				}
			}
		}
		if(is_int($this->getSubject())){
			$S=new Subject();
			$S->loadFromDB($this->getSubject());
			if(is_int($S->getSqlId()))$result['subject_name']=$S->getName();
		}
		return $result;
	}
}
?>

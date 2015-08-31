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
	private $begin;
	private $end;
	private $room        = NULL;
	private $courseType = NULL;
	private $subject     = NULL;

	const TABLENAME           = "gcourse";
	const SQLcolumns          = "id serial PRIMARY KEY, name VARCHAR(30), room VARCHAR(30), begins_at TIMESTAMP WITHOUT TIME ZONE NOT NULL, ends_at TIMESTAMP WITHOUT TIME ZONE NOT NULL, id_subject INTEGER REFERENCES gsubject(id), type INTEGER, number INTEGER, creation_timestamp TIMESTAMP WITHOUT TIME ZONE DEFAULT 'now' NOT NULL";
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
	public function __construct($newSubject=NULL, $newBegin=NULL, $newEnd=NULL)
	{
		
		if(is_int($newBegin) && is_int($newEnd)){
			if($newBegin>=$newEnd)echo "<script>console.log('Horaires de cours non conforme');</script>";
			else{
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
				}else{
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
						$this->subject = $newSubject;
						$this->begin   = $newBegin;
						$this->end    = $newEnd;
						$this->sqlId     = $result['id'];
						if(is_int($newSubject)){
							$S=new Subject();
							$S->loadFromDB($newSubject);
							$T=new Timetable();
							$T->loadFromDB($S->getTimetable());
							$T->addCourse($this);
							$G=new Group();
							$G->loadFromDB($S->getGroup());
							$T=new Timetable();
							$T->loadFromDB($G->getTimetable());
							$G->addCo
						}
					}
					
				//TODO add to all related calendars
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
		return $this->begin;
	}

	/**
	 * \brief  Getter for the attribute $begin.
	 * \return The string value of $begin.
	*/
	public function getBeginString()
	{
		return date('d/m/Y H:i', $this->begin);
	}

	/**
	 * \brief  Getter for the attribute $end.
	 * \return The value of $end.
	*/
	public function getEnd()
	{
		return $this->end;
	}

	/**
	 * \brief  Getter for the attribute $end.
	 * \return The string value of $end.
	*/
	public function getEndString()
	{
		return date('d/m/Y H:i', $this->end);
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
	 * \brief  Getter for the attribute $coursesType.
	 * \return The value of $coursesType.
	*/
	public function getCoursesType()
	{
		return $this->coursesType;
	}

	/**
	 * \brief  Getter for the attribute $coursesType.
	 * \return The string value of $coursesType.
	*/
	public function getCoursesTypeString()
	{
		switch ($this->coursesType)
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
				$this->begin = $newBegin;
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
				$this->end = $newEnd;
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
	 * \brief  Setter for the attribute $coursesType.
	 * \param  $newCoursesType Contains the new value of $coursesType.
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
	public function setSubject(Subject $newSubject=NULL)
	{
		if ($newSubject!=NULL)
		{
			$query  = "UPDATE " . self::TABLENAME . " SET id_subject = $1 WHERE id = " . $this->sqlId . ";";
			$params = array($newSubject->getSqlId());

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->subject=$newSubject;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
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
	public function integrateInTimetable(Timetable $aTimetable)
	{
		$aTimetable->addCourse($this);
	}


	/**
	 * \brief Removes the course from database.
	*/
	public function removeFromDB()
	{
		//TODO
	}

	/**
	 * \brief  Converts data about the course in HTML format.
	 * \return \e String containing data about the course.
	*/
	public function toHTML()
	{
		$result = "<p>Matière:&emsp; &emsp; " . $this->subject->getName() . "<br/>Type de cours:&emsp; &emsp;" . $this->coursesType . "&emsp; &emsp;&emsp; &emsp;Numero:&emsp; &emsp; " . $this->number . "<br/>Horaires:&emsp; du " . $this->getBeginString() . " au " . $this->getEndString() . "<br/>Salle: &emsp; &emsp; " . $this->room . "</p>";

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
 
		return $result;
	}
}
?>

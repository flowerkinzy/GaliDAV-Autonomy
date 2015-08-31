<?php
/**
 * \file    C_Subject.php
 * \brief   Defines the class Subject.
 * \details Represents a subject.
*/
error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5'))
{
	die('This file was written for PHP 5');
}
include_once("functions/error_handling.php");
require_once('classes/C_Database.php');
require_once('classes/C_Person.php');
require_once('classes/C_Group.php');
require_once('classes/C_Teacher.php');
class Subject
{
	// --- ATTRIBUTES ---
	private $sqlId     = NULL;
	private $name      = NULL;
	private $teachedByList = array();
	private $group     = NULL;
	private $timetable = NULL;

	const TABLENAME  = "gsubject";
	const SQLcolumns = "id serial PRIMARY KEY, name VARCHAR(30) NOT NULL UNIQUE, id_speaker1 INTEGER REFERENCES gperson(id), id_speaker2 INTEGER REFERENCES gperson(id), id_speaker3 INTEGER REFERENCES gperson(id), id_group INTEGER REFERENCES ggroup(id), id_calendar INTEGER REFERENCES gcalendar(id)";

	// --- OPERATIONS ---
	/**
	 * \brief Subject’s constructor
	 * \param $newName  \e String containing the subject’s name.
	 * \param $newGroup Contains the group of the subject.
	*/
	public function __construct($newName = NULL, $newGroup = NULL)
	{
		if($newGroup instanceof Group)$newGroup =$newGroup->getSqlId();
		if (is_string($newName) && is_int($newGroup)){		

			$this->name = $newName;
	 		$params     = array($newName);
			$params[]   = $newGroup;
			$query      = "INSERT INTO " . self::TABLENAME . " (name, id_group) VALUES ($1, $2);";
			$result     = Database::currentDB()->executeQuery($query, $params);

			if (!$result)
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
			else
			{
				$params = array($newName);
				$query  = "SELECT id FROM " . self::TABLENAME . " WHERE name = $1;";
				$result = Database::currentDB()->executeQuery($query, $params);

				if (!$result)
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
				else
				{
					$result = pg_fetch_assoc($result);
					$this->group  = $newGroup;
					$this->sqlId  = $result['id'];
					$newTimetable = new Timetable($this);
					$this->timetable = $newTimetable->getSqlId();
				}
			}
		}
	}

	// getters
	/**
	 * \brief  Getter for the attribute $sqlId.
	 * \return The \e integer value of $sqlId.
	*/
	public function getSqlId()
	{
		return $this->sqlId;
	}

	/**
	 * \brief  Getter for the attribute $name.
	 * \return The \e string value of $name.
	*/
	public function getName()
	{
		return $this->name;
	}

	/**
	 * \brief  Getter for the attribute $teachedByList.
	 * \return The value of $teachedByList.
	*/
	public function getTeachedByList()
	{
		return $this->teachedByList;
	}

	/**
	 * \brief  Getter for the attribute $timetable.
	 * \return The value of $timetable.
	*/
	public function getTimetable()
	{
		//TODO loadFromDB?
		return $this->timetable;
	}

	/**
	 * \brief  Getter for the attribute $group.
	 * \return The value of $group.
	*/
	public function getGroup()
	{
		//TODO loadFromDB?
		return $this->group;
	}

	// setters
	/**
	 * \brief  Setter for the attribute $name.
	 * \param  $newName Contains the new value of $name.
	*/
	private function setName($newName)
	{
		if (is_string($newName))
		{
			$query  = "UPDATE " . self::TABLENAME . " SET name = $1 WHERE id = " . $this->sqlId . ";";
			$params = array($newName);

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->name = $newName;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	/**
	 * \brief  Setter for the attribute $teachedByList.
	 * \param  $newTeachedBy Contains the new value of $teachedBy.
	*/
	public function setTeachedByList($newTeachedByList)
	{
		foreach ($this->teachedByList as $speaker)
		{
			$this->removeTeacher($speaker);
		}

		if (is_array($newTeachedByList))
		{
			foreach ($newTeachedByList as $speaker)
			{
				$this->addTeacher($speaker);
			}
		}
		
	}

	// others
	/**
	 * \brief  Checks if the course is teached by the given person.
	 * \return TRUE if the given person teaches this course, FALSE otherwise.
	*/
	public function isTeachedBy($aPerson)
	{
		if($aPerson instanceof Person)$aPerson=$aPerson->getSqlId();
		if(is_int($aPerson)){
			foreach ($this->teachedByList as $onePerson)
			{
				if ($onePerson == $aPerson)
				{
					return TRUE;
				}
			}

		
		}
		return FALSE;
	}

	/**
	 * \brief  Adds the given teacher to the course.
	 * \param  $newTeacher The teacher to add.
	*/
	public function addTeacher($newTeacher)
	{
		if (sizeof($this->teachedByList) >= 3 and isset($this->teachedByList[0]) and isset($this->teachedByList[1]) and isset($this->teachedByList[2]))
		{
			return false; //echo ('GaliDAV : 3 personnes enseignent déjà cette matière ! Remplacez-en un.');
		}
		else
		{
			if($newTeacher instanceof Person)$newTeacher=$newTeacher->getSqlId();
			if (is_int($newTeacher) && !$this->isTeachedBy($newTeacher))
			{
				$query   = "SELECT id FROM " . self::TABLENAME . " WHERE name = '" . $this->name . "';";
				$result1 = Database::currentDB()->executeQuery($query);

				if (!$result1)
				{
					Database::currentDB()->showError('Aucune matière de ce nom');
				}
				else
				{
					$result1 = pg_fetch_assoc($result1);

					if (!isset($this->teachedByList[0]))
					{
						$query = "UPDATE " . self::TABLENAME . " SET id_speaker1 = $1 WHERE id = " . $result1['id'] . ";";
					}
					else if (!isset($this->teachedByList[1]))
					{
						$query = "UPDATE " . self::TABLENAME . " SET id_speaker2 = $1 WHERE id = " . $result1['id'] . ";";
					}
					else
					{
						$query = "UPDATE " . self::TABLENAME . " SET id_speaker3 = $1 WHERE id = " . $result1['id'] . ";";
					}
					

					$params  = array($newTeacher);
					$result2 = Database::currentDB()->executeQuery($query, $params);
					if($result2){
						$this->teachedByList[]=$newTeacher;
						return true;
						//if ($result2 and ($newTeacher instanceof Teacher))
						//{
							//$this->timetable->shareWith($newTeacher);
						//}
					}else Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
			}
			else
			{
				return false;//echo ('GaliDAV : cette personne enseigne déjà cette matière');
			}
		}
		return false;
	}

	/**
	 * \brief  Removes the given teacher to the course.
	 * \param  $teacherToRemove The teacher to remove.
	*/
	public function removeTeacher($teacherToRemove)
	{
		if($teacherToRemove instanceof Person)$teacherToRemove=$teacherToRemove->getSqlId();
		if(is_int($teacherToRemove) && $this->isTeachedBy($teacherToRemove))
		{
			if ($this->teachedByList[2] == $teacherToRemove)
			{
				$query = "UPDATE " . self::TABLENAME . " SET id_speaker3 = NULL WHERE id = " . $this->sqlId . ";";

				if (Database::currentDB()->executeQuery($query))
				{
					unset($this->teachedByList[2]);
					return true;
				}
				else
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
			}

			if ($this->teachedByList[1] == $teacherToRemove)
			{
				$query = "UPDATE " . self::TABLENAME . " SET id_speaker2 = NULL WHERE id = " . $this->sqlId . ";";

				if (Database::currentDB()->executeQuery($query))
				{
					unset($this->teachedByList[1]);
					return true;
				}
				else
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
			}

			if ($this->teachedByList[0] == $teacherToRemove)
			{
				$query = "UPDATE " . self::TABLENAME . " SET id_speaker1 = NULL WHERE id = " . $this->sqlId . ";";

				if (Database::currentDB()->executeQuery($query))
				{
					unset($this->teachedByList[0]);
					return true;
				}
				else
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
			}
		}
		else
		{
			//echo ('L\'enseignant renseigné n\'enseigne pas cette matière');
			return false;
		}
		return false;
	}

	/**
	 * \brief  Loads the subject with specific id from database or the first entry (if no id defined).
	 * \param  $id The SQL id on the database.
	*/
	public function loadFromDB($id = NULL)
	{
		if (!is_int($id))
		{
			if ($this->sqlId != NULL)
			{
				$id = $this->sqlId;
			}
		}

		if (!is_int($id))
		{
			$query  = "SELECT * FROM " . self::TABLENAME . ";";
			$result = Database::currentDB()->executeQuery($query);
			if (!$result)
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
		else
		{
			$query  = "SELECT * FROM " . self::TABLENAME . " WHERE id = $1;";
			$params = array($id);
			$result = Database::currentDB()->executeQuery($query, $params);
			if (!$result)
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
		if($result){
			$result = pg_fetch_assoc($result);
			$this->loadFromRessource($result);
		}
	}

	/**
	 * \brief Loads all data from the given ressource (???).
	 * \param $ressource The ressource from which data will be loaded.
	*/
	public function loadFromRessource($ressource)
	{
		if (is_array($ressource))
		{
			$this->sqlId     = $ressource['id'];
			$this->name      = $ressource['name'];
			$this->teachedByLists = NULL;

			if ($ressource['id_speaker1'])
			{
				$this->addTeacher($ressource['id_speaker1']);
				if ($ressource['id_speaker2'])
				{
					$this->addTeacher($ressource['id_speaker2']);
					if ($ressource['id_speaker3'])
					{
						$this->addTeacher($ressource['id_speaker3']);
					}
				}

			}
			
			if ($ressource['id_group'])
			{
				$this->group=intval($ressource['id_group']);

			}
			
			if ($ressource['id_calendar'])
			{
				$this->timetable=intval($ressource['id_calendar']);

			}
			
			
		}
	}

	/**
	 * \brief Removes the subject from database.
	*/
	public function removeFromDB()
	{
		$this->timetable->removeFromDB(); // first we delete the associated calendar
		$query = "DELETE * FROM " . self::TABLENAME . " WHERE id = " . $this->sqlId . ";";

		if (!Database::currentDB()->executeQuery($query))
		{
			Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
		}
	}
	
	public function to_array(){
		$result=array();
		foreach($this as $key => $value) {
			if(!is_null($value)){
				if(!is_object($value))$result[$key] = $value;
				else {
					if(method_exists($value,"getSqlId"))$result[$key+"_id"]=$value->getSqlId();
					if(method_exists($value,"getName"))$result[$key+"_name"]=$value->getName();
				}
			}
        }
 
        return $result;
    }
	
	
}
?>

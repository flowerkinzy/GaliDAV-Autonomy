<?php
/**
 * \file    C_Group.php
 * \brief   Defines the class Group.
 * \details Represents a group, which is not necessarily a class.
*/
error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5'))
{
	die('This file was written for PHP 5');
}

include_once("functions/error_handling.php");

require_once('classes/C_Class.php');
require_once('classes/C_Timetable.php');
require_once('classes/C_ClassesTimetable.php');
require_once('classes/C_Person.php');

class Group
{
	// --- ATTRIBUTES --- // Flora: attributes shouldn’t be private since they are used by inheriting classes
	protected $name             = NULL;
	protected $isAClass         = NULL;
	protected $timetable        = NULL;
	protected $sqlId            = NULL;
	protected $studentsList     = array();
	protected $linkedGroupsList = array(); // Flora: A group can have linkedGroups and be linked to other goups.
						//A simple Group object should always be linked to one or more groups (generally classes except when it's a subgroup of a group.
						//But a Class is never linked to other groups but can have linked groups
						
						
	//Flora NOTE: A group "A" is linked to group "B" when A and B have students in common AND all courses of group A should appear in B timetable.
	// A is a linkedGroup of B and B is a dependingGroups of A.
	//Examples: a) Group ING2 is linked to Classes INFO2/NRJ2/MACS2/TEL2, b) Group INFO3ADO is linked to Class INFO3, c) Group INFO1/2 is linked to INFO1/INFO2
	//Examples: a)Classes INFO2/NRJ2/MACS2/TEL2 are dependingGroups of ING2, b) Group INFO3ADO is linked to Class INFO3, c) Group INFO1/2 is linked to INFO1/INFO2
	//NB: I f there isnt a lot of courses in common between 2 classes, there is no need to create a "super-"group like in c).
	const TABLENAME = "ggroup";
	const SQLcolumns = "id serial PRIMARY KEY, name VARCHAR(30) UNIQUE NOT NULL, is_class BOOLEAN NOT NULL DEFAULT FALSE, id_current_timetable INTEGER REFERENCES gcalendar(id),id_validated_timetable INTEGER REFERENCES gcalendar(id)";

	const composedOfTABLENAME = "ggroup_composed_of";
	const composedOfSQLcolumns = "id_person INTEGER REFERENCES gperson(id), id_group INTEGER REFERENCES ggroup(id), CONSTRAINT ggroup_composed_of_pk PRIMARY KEY(id_person, id_group)";

	const linkedToTABLENAME = "ggroup_linked_to";
	const linkedToSQLcolumns = "id_linked_group INTEGER REFERENCES ggroup(id), id_depending_group INTEGER REFERENCES ggroup(id), CONSTRAINT ggroup_linked_to_pk PRIMARY KEY(id_linked_group, id_depending_group)";

	// --- OPERATIONS ---
	/**
	 * \brief Group’s constructor
	 * \param $newName     The name of the group.
	 * \param $newIsAClass \e Boolean indicating if the group is also a class.
	*/
	public function __construct($newName = NULL, $newIsAClass = FALSE)
	{
		if (is_string($newName))
		{
			//CreateGroupAccount($newName, session_salted_sha1($newName)); // The password is the same as the group name
			$this->name      = $newName;
			$this->isAClass  = $newIsAClass;
			if($newIsAClass)
				$query           = "INSERT INTO " . self::TABLENAME . " (name, is_class) VALUES ($1, true);";
			 else
				$query           = "INSERT INTO " . self::TABLENAME . " (name, is_class) VALUES ($1, FALSE);";
			$params[]        = $newName;
			$result          = Database::currentDB()->executeQuery($query, $params);
			if (!$result)
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}else{
			  $query           = "SELECT id FROM " . self::TABLENAME . " WHERE name = $1 ;";
			  $result          = Database::currentDB()->executeQuery($query, $params);
			 
			  if($result){
				$result          = pg_fetch_assoc($result);
				$this->sqlId     = intval($result['id']);
				if ($newIsAClass)
				{
					
					//$this->timetable = new ClassesTimetable($this);
					$this->timetable = (new ClassesTimetable($this))->getSqlId();
				}
				else
				{
					// $this->timetable = new Timetable($this);
					$this->timetable = (new Timetable($this))->getSqlId();
				}
			  }else Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	// getters
	/**
	 * \brief  Getter for the attribute $studentsList.
	 * \return The \e array contained in $studentsList.
	*/
	public function getStudentsList()
	{
		return $this->studentsList;
	}

	/**
	 * \brief  Getter for the attribute $linkedGroupsList.
	 * \return The \e array contained in $linkedGroupsList.
	*/
	public function getLinkedGroupsList()
	{
		return $this->linkedGroupsList;
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
	 * \brief  Getter for the attribute $isAClass.
	 * \return The \e boolean value of $isAClass.
	*/
	public function getIsAClass()
	{
		return $this->isAClass;
	}

	/**
	 * \brief  Getter for the attribute $sqlId.
	 * \return The \e integer value of $sqlId.
	*/
	public function getSqlId()
	{
		return $this->sqlId;
	}

	/**
	 * \brief  Getter for the attribute $timetable.
	 * \return The  value of $timetable.
	*/
	public function getTimetable()
	{
		//TODO loadFromDB?
		return $this->timetable;
	}

	// setters
	/**
	 * \brief  Setter for the attribute $studentsList.
	 * \param  $newStudentsList Contains the new value of $studentsList.
	*/
	public function setStudentsList($newStudentsList = NULL)
	{
		foreach ($this->studentsList as $oneStudent)
		{
			$this->removeStudent($oneStudent);
		}

		if (is_array($newStudentsList))
		{
			foreach ($newStudentsList as $aStudent)
			{
				$this->addStudent($aStudent);
			}
		}
	}

	/**
	 * \brief  Setter for the attribute $linkedGroupsList.
	 * \param  $newLinkedGroupsList Contains the new value of $linkedGroupsList.
	*/
	public function setLinkedGroupsList($newLinkedGroupsList = NULL)
	{
		foreach ($this->linkedGroupsList as $oneLinkedGroup)
		{
			$this->removeLinkedGroup($oneLinkedGroup);
		}

		if (is_array($newLinkedGroupsList))
		{
			foreach ($newLinkedGroupsList as $aLinkedGroup)
			{
				$this->addLinkedGroup($aLinkedGroup);
			}
		}
	}

	/**
	 * \brief   Setter for the attribute $name.
	 * \details This method is declared protected because the name of a group shouldn’t change in time (or at least, groups should have different names).
	 * \param   $newName Contains the new value of $name.
	*/
	protected function setName($newName)
	{
		if (is_string($newName))
		{
			$query    = "UPDATE " . self::TABLENAME . " SET name = $1 WHERE id = " . $this->sqlId . ";";
			$params[] = $newName;

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

	// Flora: 
	/**
	 * \brief   Setter for the attribute $isAClass.
	 * \details This method is declared private because the attribute associated shouldn’t change in time (e.g.: a group won’t suddenly become a class).
	 * \param   $newIsAClass Contains the new value of $isAClass.
	*/
	private function setIsAClass($newIsAClass)
	{
		if (is_boolean($newIsAClass))
		{
			$query = "UPDATE " . self::TABLENAME . " SET is_class = " . $newIsAClass . " WHERE id = " . $this->sqlId . ";";

			if (Database::currentDB()->executeQuery($query))
			{
				$this->isAClass = $newIsAClass;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	// others
	/**
	 * \brief  Indicates if the group contains the given student.
	 * \param  $aStudent The student which will be searched in the group.
	 * \return TRUE if the given student is found, FALSE otherwise.
	*/
	public function containsStudent($aStudent)
	{
		if($aStudent instanceof Person)$aStudent=$aStudent->getSqlId();
		if(is_int($aStudent)){
		foreach ($this->studentsList as $oneStudent)
			{
				if ($oneStudent == $aStudent)
				{
					return TRUE;
				}
			}
		
		}

		return FALSE;
	}

	/**
	 * \brief  Indicates if the group is linked to the given group.
	 * \param  $aGroup The group which will be searched in the linked group’s list.
	 * \return TRUE if the given group is found, FALSE otherwise.
	*/
	public function isLinkedTo($aGroup)
	{
		if($aGroup instanceof Group)$aGroup=$aGroup->getSqlId();
		if(is_int($aGroup)){
			foreach ($this->linkedGroupsList as $oneGroup)
			{
				if ($oneGroup == $aGroup)
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * \brief  Indicates if the group depends from the given group (meaning the given group is linked to this group)
	 * \param  $aGroup 
	 * \return TRUE if the given group is found, FALSE otherwise.
	*/
	public function dependsFrom($aGroup)
	{
		if($aGroup instanceof Group){
			return $aGroup->isLinkedTo($this);
		}
		if(is_int($aGroup)){
			$G=new Group();
			$G->loadFromDB($aGroup);
			return $G->isLinkedTo($this);
		}

		return FALSE;
	}
	
	/**
	 * \brief  Adds a student.
	 * \param  $newStudent The student to add.
	*/
	public function addStudent($newStudent)
	{
		if($newStudent instanceof Person)$newStudent=$newStudent->getSqlId();
		if (is_int($newStudent) && !$this->containsStudent($newStudent))
		{
			$params[] = $newStudent->sqlId;
			$params[] = $this->sqlId;
			$query    = "INSERT INTO " . self::composedOfTABLENAME . " (id_person, id_group) VALUES ($1, $2);";
			$result   = Database::currentDB()->executeQuery($query, $params);

			if ($result)
			{
				$this->studentsList[] = $newStudent;
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	/**
	 * \brief  Removes a student.
	 * \param  $studentToRemove The student to remove.
	*/
	public function removeStudent( $studentToRemove)
	{
		if($studentToRemove instanceof Person)$studentToRemove=$studentToRemove->getSqlId();
		if (is_int($studentToRemove) && $this->containsStudent($studentToRemove))
		{
			$query = "DELETE FROM " . self::composedOfTABLENAME . " WHERE id_person = " . $studentToRemove . " AND id_group = " . $this->sqlId . ";";

			if (Database::currentDB()->executeQuery($query))
			{
				// Flora: we have already checked the array contains the student to remove
				unset($this->studentsList[array_search($studentToRemove, $this->studentsList)]);
			}
			else
			{
				Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
			}
		}
	}

	/**
	 * \brief  Adds a linked group.
	 * \param  $newLinkedGroup The linked group to add.
	*/
	public function addLinkedGroup($newLinkedGroup)
	{
		if($newLinkedGroup instanceof Group)$newLinkedGroup=$newLinkedGroup->getSqlId();
		if (is_int($newLinkedGroup) )
		{
			$params[] = $newLinkedGroup;
			$params[] = $this->sqlId;
			$query    = "SELECT * FROM " . self::linkedToTABLENAME . " WHERE id_linked_group = $1 AND id_depending_group=$2;";

			if (Database::currentDB()->executeQuery($query, $params))
			{
				$this->linkedGroupsList[] = $newLinkedGroup;
				// there’s nothing more to do since the DB seems to contain a link between the 2 groups
				// $newLinkedGroup should already have $this in its linkedGroupsList array.
			}
			else
			{
				$query = "INSERT INTO " . self::linkedToTABLENAME . " (id_linked_group, id_depending_group) VALUES ($1, $2);";

				$result = Database::currentDB()->executeQuery($query, $params);

				if ($result)
				{
					$this->linkedGroupsList[] = $newLinkedGroup;
				}
				else
				{
					Database::currentDB()->showError("ligne n°" . __LINE__ . " classe :" . __CLASS__);
				}
			}
		}
	}

	/**
	 * \brief  Removes a linked group.
	 * \param  $linkedGroupToRemove The linked group to remove.
	*/
	public function removeLinkedGroup($linkedGroupToRemove)
	{
		if($linkedGroupToRemove instanceof Group)$linkedGroupToRemove=$linkedGroupToRemove->getSqlId();
		if (is_int($linkedGroupToRemove) && $this->isLinkedTo($linkedGroupToRemove))
		{
			$params[] = $newLinkedGroup;
			$params[] = $this->sqlId;
			$query = "DELETE FROM " . self::linkedToTABLENAME . " WHERE (id_linked_group = $1 AND id_depending_group = $2);";

			if (Database::currentDB()->executeQuery($query))
			{
				// Flora: we have already checked the array contains the group to remove
				unset($this->linkedGroupsList[array_search($linkedGroupToRemove, $this->linkedGroupsList)]);
			}
			else
			{
				// No error shown because it could happen the entry has already been removed by the group $G.
			}

		}
	}
	
	/**
	 * \brief  
	 * \return The list of all courses between $begin and $end  to display for this group including all courses from  linkedGroup
	*/
	public function getAllCoursesListBetween($begin,$end)
	{
		if(is_int($begin) && is_int($end)){
			$result=array();
			$T=new Timetable();
			$T->loadFromDB($this->timetable);
			if(is_int($T->getSqlId()))
				return $T->getCoursesListBetween($begin,$end);
		}
		return null;
	}

	/**
	 * \brief  Loads data from the database.
	 * \param  $id The SQL id on the database.
	 * \param  $canBeClass \e Boolean indicating if the group is a class.
	 * \return TRUE if data loaded successfully, FALSE otherwise.
	*/
	public function loadFromDB($id = NULL, $canBeClass = TRUE) // $id can be an integer (id) or a string (name).
	{
		if ($id == NULL) // if we do not want to load a particular group
		{
			if ($this->sqlId != NULL) // check if the current group object is defined
			{
				$id = $this->sqlId; // if yes, we want to “reload” data about this object from the database (UPDATE)
			}
		}

		if ($id == NULL) // if no, the first group object of the DB will be chosen to be loaded
		{
			if ($canBeClass)
			{
				$query = "SELECT * FROM " . self::TABLENAME . ";";
			}
			else
			{
				$query = "SELECT * FROM " . self::TABLENAME . " WHERE is_class = FALSE";
			}

			$result = Database::currentDB()->executeQuery($query);
		}
		else // from here, we load data about the group that has $id as sqlId
		{
			if (is_int($id))
			{
				if ($canBeClass) // we load any group that matches the criteria
				{
					$query = "SELECT * FROM " . self::TABLENAME . " WHERE id = $1;";
				}
				else // we load only basic group (not class) that matches the criteria
				{
					$query = "SELECT * FROM " . self::TABLENAME . " WHERE id = $1 AND is_class = FALSE;";
				}				
			}
			else if (is_string($id))
			{
				if ($canBeClass) // we load any group that matches the criteria
				{
					$query = "SELECT * FROM " . self::TABLENAME . " WHERE name = $1;";
				}
				else // we load only basic group (not class) that matches the criteria
				{
					$query = "SELECT * FROM " . self::TABLENAME . " WHERE name = $1 AND is_class = FALSE;";
				}
			}

			$params = array($id);
			$result = Database::currentDB()->executeQuery($query, $params);
		}

		if ($result)
		{
			$result = pg_fetch_assoc($result); // $result is now an array containing values for each SQLcolumn of the group table
			$this->loadFromRessource($result);

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * \brief  Loads data from the given ressource.
	 * \param  $ressource The ressource from which the data will be loaded.
	*/
	public function loadFromRessource($ressource)
	{
		if (is_array($ressource))
		{
			// We change the value of attributes.
			$this->sqlId    = $ressource['id'];
			$this->name     = $ressource['name'];
			$this->isAClass = $ressource['is_class'];

			if (is_int($ressource['id_current_timetable']))
			{
				$this->timetable = intval($ressource['id_current_timetable']);
			}

			// We load each element of the arraylist of students (students are people registered in people table).
			$this->studentsList   = NULL;
			$query                = "SELECT * FROM " . self::composedOfTABLENAME . " WHERE id_group = " . $this->sqlId . ";";
			$result               = Database::currentDB()->executeQuery($query);
			$ressource            = pg_fetch_assoc($result);

			while ($ressource)
			{
				$this->addStudent($ressource['id_person']);
				$ressource = pg_fetch_assoc($result);
			}

			// We load each element of the arraylist of linked groups (which are groups registerd in group table).
			$this->linkedGroupsList   = NULL;
			$query                    = "SELECT * FROM " . self::linkedToTABLENAME . " WHERE id_depending_group = " . $this->sqlId . ";";
			$result                   = Database::currentDB()->executeQuery($query);
			$ressource                = pg_fetch_assoc($result);

			while ($ressource)
			{
				$this->addLinkedGroup($ressource['id_linked_group']);
				$ressource = pg_fetch_assoc($result);
			}
		}
	}

	/**
	 * \brief   Loads student from the given ressource.
	 * \details This method expects a ressource resulting of a select query on composedOf table.
	 * \param   $ressource The ressource from which the student will be loaded.
	*/
	
//DEPRECATED
	public function loadStudentFromRessource($ressource)
	{
		if (is_array($ressource))
		{
			//$aStudent = new Person();
			//$aStudent->loadFromDB(intval($ressource['id_person']));
			//$this->addStudent($aStudent);
			$this->addStudent($ressource['id_person']);
		}
	}

	/**
	 * \brief   Loads linked group from the given ressource.
	 * \details This method expects a ressource resulting of a select query on linkedTo table.
	 * \param   $ressource The ressource from which the linked group will be loaded.
	*///DEPRECATED
	public function loadLinkedGroupFromRessource($ressource)
	{
		if (is_array($ressource))
		{
			//$aLinkedGroup = new Group();
			//$aLinkedGroup->loadFromDB(intval($ressource['id_group']));
			//$this->addLinkedGroup($aLinkedGroup);
			$this->addLinkedGroup($ressource['id_group']);
		}
	}

	/**
	 * \brief   Removes data from the database.
	 * \details Beware, this method may imply many irreversible changes in database. After this method, all Group’s instances should be reloaded from database, to stay reliable.
	*/
	public function removeFromDB()
	{
		$this->setStudentsList(); // remove all students from the group (DB)
		$this->setLinkedGroupsList(); // remove all links with other groups/classes (DB)
		$params = array(intval($this->sqlId));
		$T=new Timetable();
		$T->loadFromDB($this->timetable);
		$T->removeFromDB();
		
		$query  = "SELECT id FROM " . Subject::TABLENAME . " WHERE id_group = $1;";
		$result = Database::currentDB()->executeQuery($query, $params);

		if ($result)
		{
			$array = pg_fetch_assoc($result);

			while ($array != NULL)
			{
				$array    = $array['id'];
				$aSubject = new Subject();

				if ($S->loadFromDB($array['id']))
				{
					$S->removeFromDB();
				}

				$array = pg_fetch_assoc($result);
			}
		}

		$query = "DELECT FROM " . self::TABLENAME . " WHERE id = $1;";

		if (Database::currentDB()->executeQuery($query, $params))
		{			
			/*$DB = new Database("davical_app", "davical");

			if (!$DB->connect())
			{
				echo("Pas de connexion vers davical.");
			}
			else
			{
				$params = array($this->name);
				$query2 = "DELETE FROM dav_principal WHERE username = $1;";
				$DB->executeQuery($query2, $params);
				$DB->close();
			}
			*/
		}
		else
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

public function to_array(){
		$result=array();
        foreach($this as $key => $value) {
			if(!is_null($value)){
			/* 		$result[$key] = NULL;
			}
			else { */
				if(!is_object($value))$result[$key] = $value;
				else $result[$key]=$value->to_array();
			}
        }
 
        return $result;
    }
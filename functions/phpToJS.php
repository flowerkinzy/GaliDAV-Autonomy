<?php
function object_to_array($data)
{
    if(is_array($data) || is_object($data))
    {
        $result = array();
 
        foreach($data as $key => $value) {
			if(!is_null($value)){
			/* 		$result[$key] = NULL;
			}
			else { */
				$result[$key] = $this->object_to_array($value);
			}
        }
 
        return $result;
    }
 
    return $data;
}
?>
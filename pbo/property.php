<?php

require_once 'pdo/db_mysql_pdo.php';

class Property extends Base {
	
	protected static $TABLE = 'properties';
	protected static $FIELDS = array('id','bathrooms','bedrooms','rent','car_spaces','address','property_type');
	
	const BATHROOMS_MAX = 9;
	const BEDROOMS_MAX = 9;
	const RENT_MAX = 9999;
	const CAR_SPACES_MAX = 9;
	const LEN_ADDRESS_MAX = 200;
	const LEN_PROPERTY_TYPE_MAX = 20;
	
	function __construct($in=NULL){
			
		$this->loadFromDB($in);

		parent::__construct();
	}

	function get($id) {
		
		return $this->loadFromDB($this->dp->get($this::$TABLE, $id));		
	}	
	
	private function loadFromDB($in){
		
		$this->fields = null;
		
		if ($in!==NULL)
		{
			foreach ($this::$FIELDS as $field) 
			{
				if(isset($in[$field]))
				{
					$this->fields[$field]=$in[$field];
				}
				else {
					$this->fields[$field]=null;
				}
			}
		}
		
		return $this->fields;
		
	}
	
	function validate($id, $data){		
		
		global $g_REST_call;
		$property=array();
		
		foreach ($this::$FIELDS as $field) 
		{
			
			if ($field==='bathrooms')
			{
				if (!isset($data[$field]) || $data[$field]===''){
					$this->error[$field] = 'You must enter number of bathrooms.';
				}
				else if (!$this::validBathrooms($data[$field])){
					$this->error[$field] = 'An invalid number of bathrooms was entered.';
				}
				else
				{
					$property[$field]=$data[$field];
				}
			}
			else if ($field==='bedrooms')
			{
				if (!isset($data[$field]) || $data[$field]===''){
					$this->error[$field] = 'You must enter number of bedrooms.';
				}
				else if (!$this::validBedrooms($data[$field])){
					$this->error[$field] = 'An invalid number of bedrooms was entered.';
				}
				else
				{
					$property[$field]=$data[$field];
				}
			}
			else if ($field==='rent')
			{
				if (!isset($data[$field]) || $data[$field]===''){
					$this->error[$field] = 'You must enter the rent.';
				}
				else if (!$this::validRent($data[$field])){
					$this->error[$field] = 'An invalid amount of rent was entered.';
				}
				else
				{
					$property[$field]=$data[$field];
				}
			}
			else if ($field==='car_spaces')
			{
				if (!isset($data[$field]) || $data[$field]===''){
					$this->error[$field] = 'You must enter number of car spaces.';
				}
				else if (!$this::validCarSpaces($data[$field])){
					$this->error[$field] = 'An invalid number of car spaces was entered.';
				}
				else
				{
					$property[$field]=$data[$field];
				}
			}
			else if ($field==='address')
			{
				if (!isset($data[$field]) || $data[$field]===''){
					$this->error[$field] = 'You must enter the address.';
				}
				else if (!$this::validAddress($data[$field])){
					$this->error[$field] = 'An invalid address was entered.';
				}
				else
				{
					$property[$field]=$data[$field];
				}
			}
			else if ($field==='property_type')
			{
				if (!isset($data[$field]) || $data[$field]===''){
					$this->error[$field] = 'You must enter the property type.';
				}
				else if (!$this::validPropertyType($data[$field])){
					$this->error[$field] = 'An invalid property type was entered.';
				}
				else
				{
					$property[$field]=$data[$field];
				}
			}
			else{
			
				$property[$field]=isset($data[$field]) ? $data[$field] : '';
			}
		}
		
		if (count($this->error)!==0)
		{
			if ($g_REST_call)
			{
				// If this is a REST call then we take advantage of Restler's Responder class for giving a structure to the error 
				// and success response. We assign $error to the $data field of MyResponder, the extended Responder class: 
				MyResponder::$data=$this->error;
				throw new RestException(417,"Validation errors.");
			}
			else
			{
				// For a normal PHP POST, the calling context will extract $error from the object:
				//TODO: replace with a more relevant exception
				throw new InvalidPropertyArgumentException("");
			}
		}
		
		return $property;
	}
	
	static function validBathrooms($in_bathrooms)
	{
		$in_bathrooms=trim($in_bathrooms);
	    try {
	    	$in_bathrooms=intval($in_bathrooms);
        } catch (Exception $e) 
        {
        	return false;
        }
        return ($in_bathrooms > 0 and $in_bathrooms <= Property::BATHROOMS_MAX);
			}
	static function validBedrooms($in_bedrooms)
	{
		$in_bedrooms=trim($in_bedrooms);
	    try {
	    	$in_bedrooms=intval($in_bedrooms);
        } catch (Exception $e) 
        {
        	return false;
        }
        return ($in_bedrooms > 0 and $in_bedrooms <= Property::BEDROOMS_MAX);
	}
	static function validRent($in_rent)
	{
		$in_rent=trim($in_rent);
	    try {
	    	$in_rent=intval($in_rent);
        } catch (Exception $e) 
        {
        	return false;
        }
        return ($in_rent > 0 and $in_rent <= Property::RENT_MAX);
	}
	static function validCarSpaces($in_car_spaces)
	{
		$in_car_spaces=trim($in_car_spaces);
	    try {
	    	$in_car_spaces=intval($in_car_spaces);
        } catch (Exception $e) 
        {
        	return false;
        }
        return ($in_car_spaces > 0 and $in_car_spaces <= Property::CAR_SPACES_MAX);
	}
	static function validAddress($in_address)
	{
		$in_address = trim($in_address);
		return (strlen($in_address) <= Property::LEN_ADDRESS_MAX);
	}
	static function validPropertyType($in_property_type)
	{
		$in_property_type = trim($in_property_type);
		return (strlen($in_property_type) <= Property::LEN_PROPERTY_TYPE_MAX);
	}
}

?>
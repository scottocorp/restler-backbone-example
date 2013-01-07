<?php

class Base {

	protected static $TABLE = '';
	protected static $FIELDS = array();
	public $fields = null;
	public $error = array();
	protected $dp;

	function __construct($in=NULL){

		$this->dp = DB_MySQL_PDO::getInstance();
	}

	function index(){
		return $this->getAll();
	}
	function getAll(){
		return $this->dp->getAll($this::$TABLE);
	}
	function get($id) {
		$this->fields=$this->dp->get($this::$TABLE, $id);
		return $this->fields;
	}
	function post($request_data=NULL) {
		if ($request_data===NULL)
		{
			$this->fields=$this->dp->insert($this::$TABLE, $this->validate(null, $this->fields));
			return $this->fields;
		}
		else
		{
			$this->fields=$this->dp->insert($this::$TABLE, $this->validate(null, $request_data));
			return $this->fields;
		}
	}
	function put($id, $request_data=NULL) {
		if ($request_data===NULL)
		{
			$this->fields=$this->dp->update($this::$TABLE, $id, $this->validate($id, $this->fields));
			return $this->fields;
		}
		else {
			$this->fields=$this->dp->update($this::$TABLE, $id, $this->validate($id, $request_data));
			return $this->fields;
		}
	}
	function delete($id) {

		$this->fields=$this->dp->delete($this::$TABLE, $id);
		return $this->fields;

	}

	function validate($id, $data){
	}
}
?>
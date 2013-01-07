<?php
/**
 * MySQL DB actions 
 */

// DB connection info
require_once 'pdo/conn.php';

class DB_MySQL_PDO
{
    private static $db_instance;
    private $pdo;
    
    public static function getInstance()
    {
    	// Only one instance of this class should ever exist. 
    	// $db_instance will hold a refernce to this instance and we create and return it here.
    	// (This is why the constructor has been made private below. It should not be invoked externally) 
        if (DB_MySQL_PDO::$db_instance === NULL)
        {
        	DB_MySQL_PDO::$db_instance = new DB_MySQL_PDO();
        
        	try {
          		DB_MySQL_PDO::$db_instance->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_DBASE, DB_USER, DB_PASS);
          		DB_MySQL_PDO::$db_instance->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
        	} catch (PDOException $e) {
          		//throw new RestException(501, 'MySQL: ' . $e->getMessage());
        		throw new DatabaseErrorException($e->getMessage());
        	}
      	}
      	return DB_MySQL_PDO::$db_instance;
    }    
    private function __construct ()
    {
    }
    function get ($table, $id, $installTableOnFailure = FALSE)
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
        	$sql = $this->pdo->prepare('SELECT * FROM '.$table.' WHERE id=:id');
        	$sql->execute(array(':id' => $id));
        	return $this->id2int($sql->fetch());
        	 
        } catch (PDOException $e) {
            //throw new RestException(501, 'MySQL: ' . $e->getMessage());
        	throw new DatabaseErrorException($e->getMessage());
        }
    }
    function getAll ($table, $installTableOnFailure = FALSE)
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $stmt = $this->pdo->query('SELECT * FROM '.$table);
            return $this->id2int($stmt->fetchAll());
        } catch (PDOException $e) {
            //throw new RestException(501, 'MySQL: ' . $e->getMessage());
        	throw new DatabaseErrorException($e->getMessage());
        }
    }
    function insert ($table, $rec)
    {
    	foreach ($rec as $key => $value)
    	{
    		if ($key!="id")
    		{
    			$tmp_col[] = $key;
    			$tmp_col2[] = ":".$key;
    			$executeArray[":".$key] = $value;
    		}
    	}
    	$columns = join(",", $tmp_col);
    	$columns2 = join(",", $tmp_col2);
    	
    	$sql = $this->pdo->prepare("INSERT INTO ".$table."(".$columns.")VALUES(".$columns2.")");
    	if (!$sql->execute($executeArray))
    		return FALSE;
    	 
    	return $this->get($table, $this->pdo->lastInsertId());
    }
    function update ($table, $id, $rec)
    {
    	foreach ($rec as $key => $value)
    	{
    		$executeArray[":".$key] = $value;
    		if ($key!="id")
    		{
				$tmp_setting_list[] = $key."=:".$key;
    		}
    	}
    	$setting_list = join(",", $tmp_setting_list);

    	$sql = $this->pdo->prepare("UPDATE ".$table." SET ".$setting_list." WHERE id = :id");
    	if (!$sql->execute($executeArray))
    		return FALSE;
    	 
        return $this->get($table, $id);
    }
    function delete ($table, $id)
    {
        $r = $this->get($table, $id);
        if (!$r || !$this->pdo->prepare("DELETE FROM ".$table." WHERE id = ?")->execute(array($id)))
        	return FALSE;
        return $r;
    }
    function execute ($sql)
    {
        $r = $this->pdo->query($sql);
        if (! $r )
        	return FALSE;
        return $r->fetchAll();
    }
    private function id2int ($r)
    {
        if (is_array($r)) {
            if (isset($r['id'])) {
                $r['id'] = intval($r['id']);
            } else {
                foreach ($r as &$r0) {
                    $r0['id'] = intval($r0['id']);
                }
            }
        }
        return $r;
    }
}
?>
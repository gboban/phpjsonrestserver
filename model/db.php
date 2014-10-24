<?php
class Database{
	private static $_db = null;
	
	public static function get_instance(){
		if(!self::$_db){
			self::$_db = new CMYSQLi();
		
			self::$_db->connect();
		}
		return self::$_db;
	}
}
class CMYSQLi{
	private $connection;
	
	public function connect(){
		if(!$this->connection){
			try{
				$this->connection =  new mysqli(
						"localhost",
						"root",
						"",
						"omega"
				);
				// check for error
				if($this->connection->connect_errno){
					/*
					 * we want connect_error to be displayed only in debug mode
					 * (i.e: it displays error message like Access denied for user@host)
					trigger_error(
							"Error connectiong database: " . $this->connection->connect_error,
							E_USER_ERROR
					);
					*/
					trigger_error(
							"Error connectiong database...",
							E_USER_ERROR
					);
				}
			}catch(Exception $e){
				print("Conncetion filed: " . $e->getMessage());
			}
		}
		return $this->connection;
	}
	
	public function query($sql){
		$rows = array();
//print("<hr />".$sql."<hr />");
//$rows = array('sql'=>$sql);
		$result = $this->connection->query($sql);
		if(!$result){
			trigger_error(
					"Error executing query..." . $sql,
					E_USER_ERROR
			);
		}
		while($row = $result->fetch_array()){
			array_push($rows, $row);
		}
		$result->free();

		return $rows;
	}
	
	public function execute($sql){
		$rows = array();
		$result = $this->connection->query($sql);
		if(!$result){
			trigger_error(
					"Error executing query..." . $sql,
					E_USER_ERROR
			);
		}

		return $result;
	}
	
	public function get_last_id(){
		return $this->connection->insert_id;
	}
}
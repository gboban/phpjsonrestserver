<?php
require_once("db.php");

class Contact{
	private $db = null;
	
	protected function get_db(){
		if(!$this->db){
			$this->db = Database::get_instance();
		}

		return $this->db;
	}
	
	public function get_contact($id){
		$sql = "select * from contact where contact_id=" . $id;
		$an_row = null;
		
		$rows = $this->get_db()->query($sql);
		foreach($rows as $key=>$value){
			$rows[$key]['phones'] = $this->get_phones($value['contact_id']);
		}
		if(isset($rows[0])){
			$an_row = $rows[0];
		}
		return $an_row;
	}
	
	public function get_contacts($pattern, $first, $count){
		$rows = array();
		$sql = "select * from contact where contact_name LIKE '".$pattern."' or contact_surname LIKE  '".$pattern."' or contact_desc LIKE  '".$pattern."' ";
		$sql .= " limit " . $first . ", " . $count;

		$rows = $this->get_db()->query($sql);
		foreach($rows as $key=>$value){
			$rows[$key]['phones'] = $this->get_phones($value['contact_id']);
		}
//print("<hr />");
//print_r($rows);
//print("<hr />");
		return $rows;
	}
	
	public function add_contact($name, $surname, $city, $desc, $imgpath){
		$sql = "insert into contact";
		$sql .= " (contact_name, contact_surname, contact_city, contact_desc, contact_imgpath)";
		$sql .= " values('".$name."', '".$surname."', '".$city."', '".$desc."', '".$imgpath."')";
		
		$result = $this->get_db()->execute($sql);
		if($result){
			return $this->get_db()->get_last_id();
		}
		return $result;
	}
	
	public function update_contact($id, $name, $surname, $city, $desc, $imgpath){
		$sql = "update contact set";
		$sql .= " contact_name = '".$name."',";
		$sql .= " contact_surname = '".$surname."',";
		$sql .= " contact_city = '".$city."',";
		$sql .= " contact_desc = '".$desc."',";
		$sql .= " contact_imgpath = '".$imgpath."'";
		$sql .= " where contact_id=" . $id;

		$result = $this->get_db()->execute($sql);
		
		return $result;
	}
	
	public function delete_contact($id){
		
		$sql = "delete from contact where contact_id=" . $id;
		
		$result = $this->get_db()->execute($sql);
		return $result;
	}
}
?>
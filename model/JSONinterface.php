<?php
/*
 * NOTEs:
 * 	- annotations are for php2wsdl if required
 */
require_once("model/wsimplementation.php");

class JSONInterface{
	private $_impl;
	
	protected function get_impl(){
		if(!$this->_impl){
			$this->_impl = new Contact();
		}
		
		return $this->_impl;
	}
	
	/*
	* Interface methods
	*
	*  [GET, PUT, POST, DELETE]
	*/
	public function get($id){
		$result = null;
		if($id!=null){
			// get object
			$result = $this->get_contact($id);
		}else{
			trigger_error(
					"500 Internal Server Error",
					E_USER_ERROR
			);
		}

		return $result;
	}
	
	public function get_all(){
		$result = null;
		$result = $this->get_contacts(array('pattern'=>'%'));
	
		return $result;
	}
	
	public function delete($id){
		$result = null;
	
		if($id != null){
			$result = $this->delete_contact($id);
		}else{
			trigger_error(
					"500 Internal Server Error",
					E_USER_ERROR
			);
		}
	
		return $result;
	}
	
	public function put($id, $obj){
		$result = null;
	
		if($id != null){
			$result = $this->update_contact($id, $obj);
		}
	
		return $result;
	}
	
	protected function post($obj){
		$result = null;
	
		$result = $this->add_contact($obj);
		if(!$result){
			trigger_error(
					"500 Internal Server Error",
					E_USER_ERROR
			);
		}
	
		return $result;
	}
	
	/**
	 * returns an contact by id
	 *
	 * @param int $id
	 * @return Contact
	 */
	public function get_contact($id){
		$result = $this->get_impl()->get_contact($id);

		return $result;
	}
	
	/**
	 * returns array of contacts depending on $pattern
	 *
	 * @param string $pattern
	 * @return ContactList
	 */
	public function get_contacts($args){
		$json = "";
		$result = $this->get_impl()->get_contacts(
				$args['pattern'],
				$args['first'],
				$args['count']
		);

		return $result;
	}

	/**
	 * adds new contact to the database and returns its id
	 *
	 * @param string $name
	 * @param string $surname
	 * @param string $desc
	 * @param string $imgpath
	 * @return integer
	 */
	public function add_contact($obj){
		$result = $this->get_impl()->add_contact(
			$obj->contact_name,
			$obj->contact_surname,
			$obj->contact_city,
			$obj->contact_desc,
			$obj->contact_imgpath
		);
		
		return $result;
	}
	
	/**
	 * updates contact
	 *
	 * @param integer $id
	 * @param object $obj
	 * @return boolean
	 */
	public function update_contact($id, $obj){
		$result = $this->get_impl()->update_contact(
			$id,
			$obj->contact_name,
			$obj->contact_surname,
			$obj->contact_city,
			$obj->contact_desc,
			$obj->contact_imgpath		
		);

		return $result;
	}
	
	/**
	 * deletes contact
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete_contact($id){
		$result = $this->get_impl()->delete_contact($id);
		
		return $result;
	}
}
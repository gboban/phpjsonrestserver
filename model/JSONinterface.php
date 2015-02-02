<?php
/**
 * @author goran
 * @filesource
 *
 * JSONServer - handles JSON requests and dispatches them to appropriete objects
 * Enforces RESTful design
 *
 */
/**
 * This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once("Contact.php");

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
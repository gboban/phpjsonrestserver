<?php
/**
 * @author goran
 * @filesource
 * 
 * JSONServer - handles JSON requests and dispatches them to appropriete objects
 * Enforces RESTful design
 *
 */
namespace jsonserver;

class JsonServer{
	private $_interfaces = array();

	protected function get_data() {
		$contents = null;
		
		try{
			$contents = file_get_contents('php://input');
		}catch(Exception $e){
			trigger_error(
					"Error reading php://input..." . $e->getMessage(),
					E_USER_ERROR
			);
		}
		return $contents;
	}
	
	public function addInterface($resource, $obj){
		// interface should implement get, post, put, delete methods
		$this->_interfaces[$resource] = $obj;
	}
	
	public function getInterfaces(){
		return $this->_interfaces;
	}
	
	protected function dispatch($verb, $resource, $id, $obj){
		$interface = $this->interfaces['$resource'];
		
		switch($verb){
			case 'GET':
				// if $id == null => get_all
				if($id == null){
					$result = $interface->get_all();
					if(!$result){
						trigger_error(
								"404 Not Found",
								E_USER_ERROR
						);
					}
				}else{
					$result = $interface->get($id);
					if(!$result){
						trigger_error(
								"404 Not Found",
								E_USER_ERROR
						);
					}
				}
				break;
			case 'POST':
				if($obj != null){
					$result = $interface->post($obj);					
				}else{
					trigger_error(
							"400 Bad Request",
							E_USER_ERROR
					);
				}
				break;
			case 'PUT':
				if(($obj != null) && ($id != null)){
					$result = $interface->put($id, $obj);					
				}else{
					trigger_error(
							"400 Bad Request",
							E_USER_ERROR
					);
				}
				break;
			case 'DELETE':
				// id should not be null
				if($id != null){
					$result = $interface->delete($id);
				}else{
					trigger_error(
							"400 Bad Request",
							E_USER_ERROR
					);
				}
				break;
			default:
				trigger_error(
						"501 Not Implemented",
						E_USER_ERROR
				);
		}
		
		return $result;
	}
	
	protected function _do_handle(){
		// expect resource and id in GET params, rest in php://input
		$_verb = '';
		$_resource = '';
		$_current_interface = null;
		$_id = null;
		$_data = null;
		
		// find out request verb
		$_verb = $_SERVER['REQUEST_METHOD'];
		// check verb
		if(!in_array($_verb, array('GET', 'PUT', 'POST', 'DELETE'))){
			// 501 Not Implemented
			trigger_error(
					"501 Not Implemented",
					E_USER_ERROR
			);
		}
		
		// get resource name
		$available_resources = array_keys($this->_interfaces);
		try{
			$_resource = $_GET['resource'];
			// check if resource exists
			if(!array_key_exists($_resource, $available_resources)){
				trigger_error(
						"404 Not Found",
						E_USER_ERROR
				);
			}
		}catch(Exception $e){
			// no resource given - try first in interfaces
			if(count($available_resources) > 0){
				$_resource = $available_resources[0];
			}else{
				trigger_error(
						"404 Not Found",
						E_USER_ERROR
				);
			}
		}
		
		$_interface = $this->_interfaces[$_resource];
		
		// get id (if present)
		try{
			$_id = $_GET['id'];
		}catch(Exception $e){
			// not an error - just keep id being null
			$_id = null;
		}
		
		// get data - should be used nly for PUT and POST requests
		if(in_array($_verb, array('PUT', 'POST'))){
			try{
				$_data = get_data();
			}catch(Exception $e){
				trigger_error(
						"400 Bad Request",
						E_USER_ERROR
				);
			}
		}
		
		if(($_data != null) && ($_data != '')){
			try{
				$_obj = json_decode($_data);
			}catch(Exception $e){
				$_obj = null;
			}
		}
		
		// dispatch request
		$result = $this->dispatch($_verb, $_resource, $_id, $_obj);
		return $result;
	}
	
	public function handle(){
		try{
			$result = $this->_do_handle();
			// print json & exit
			try{
				$jdata = json_encode($result);
				header('Content-Type: application/json');
				print($jdata);
			}catch(Exception $e){
				trigger_error(
					"500 Internal Server Error",
					E_USER_ERROR
				);
			}
		}catch(Exception $e){
			header("HTTP/1.0 " . $e->getMessage());
		}
	}
}

?>
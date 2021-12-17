<?php
namespace Controllers;
use \Router\Router;
use \Controllers\User;

class App extends Router {

	public User $user;

	/**
	 */
	function __construct() {
		parent::__construct();
		$this->user = new User();
	}

	private function controller() {
		;
	}

	public function response() {
		if ($this->is_route_registered($this->request_route)) { // http_response_code(200);
			header('HTTP/1.1 200 OK');
			// echo(__DIR__.'<br/>');
			require_once(__DIR__.'/'.$this->_routes_registered[$this->request_route].'.php');
		}
		else {
			header('HTTP/1.1 404 Not Found');
			die('404 - Not Found');
		}

	}

}

?>
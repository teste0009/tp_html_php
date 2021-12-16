<?php
namespace Controllers;
use \Router\Router;

class App extends Router {

	/**
	 */
	function __construct() {
		parent::__construct();
	}

	private function controller() {
		switch ($this->request_route) {
			case '/':
				require_once 'index.class.php';
				break;

			default:
				;
				break;
		}
	}

	public function response() {
		if ($this->is_route_registered($this->request_route)) { // http_response_code(200);
			header('HTTP/1.1 200 OK');
			echo(__DIR__.'<br/>');
			require_once(__DIR__.'/'.$this->_routes_registered[$this->request_route].'.class.php');
		}
		else {
			header('HTTP/1.1 404 Not Found');
			die('404 - Not Found');
		}

	}

}

?>
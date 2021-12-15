<?php

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

}

$app = new App();

if ($app->is_route_registered($app->request_route)) { // http_response_code(200);
	header('HTTP/1.1 200 OK');
	require_once('controllers/'.$app->_routes_registered[$app->request_route].'.class.php');
}
else {
	header('HTTP/1.1 404 Not Found');
	die('404 - Not Found');
}
?>
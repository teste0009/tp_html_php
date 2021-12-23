<?php
namespace Controllers;
use \Router\Router;
use \Controllers\User;

class App extends Router {

	public User $user;
	public string $name = 'RRHH - PHP';
	public string $title = '';
	public string $content_html = '';
	public array $_nav_options = [
		['name' => 'Inicio',                       'route' => '/'],
		['name' => 'Empleados',                    'route' => '/empleados'],
		['name' => 'Evaluaciones',                 'route' => '/evaluaciones'],
		['name' => 'Rotaci&oacute;n del Personal', 'route' => '/rotacion'],
		['name' => 'Logout',                       'route' => '/logout'],
	];

	function __construct() {
		parent::__construct();
		$this->user = new User();
		$this->controller();
	}

	private function chkPost() {
		show_between_pre_tag($_POST, "\$_POST");
	}

	public function login() {
		;
	}

	private function controller() {
		$this->chkPost();
	}

	public function response() {
		if ($this->is_route_registered($this->request_route)) { // http_response_code(200);
			header('HTTP/1.1 200 OK'); // echo(__DIR__.'<br/>');
			if ( ! $this->user->isLogged) {
				if ($this->request_route != '/') {
					$this->request_route = '/';
					header('Location: '.$this->base_folder.'/');
				}
			}
			require_once(__DIR__.'/'.$this->_routes_registered[$this->request_route].'.php');
		}
		else {
			header('HTTP/1.1 404 Not Found');
			die('404 - Not Found');
		}

	}

}

?>
<?php
namespace Router;

class Router  {
  private $doc_root = "";
  public $http_host = "";
	public $base_folder = "/CFP18/tp_html_php";
	public $index_script = "/index.php";

  public $request_route = "";
  public $_routes_registered = [];

  function __construct() {
		// show_between_pre_tag($_SERVER, "\$_SERVER");

    if (empty($_GET)) parse_str($_SERVER['REDIRECT_QUERY_STRING'], $_GET);
    if (empty($_REQUEST)) parse_str($_SERVER['REDIRECT_QUERY_STRING'], $_REQUEST);

    // show_between_pre_tag($_GET, "\$_GET");
    // show_between_pre_tag($_REQUEST, "\$_REQUEST");

		$request_uri = rtrim($_SERVER['REDIRECT_URL'], " /"); // echo("\$request_uri = "); var_dump($request_uri); echo("<br/>");
		$this->request_route = str_replace([$this->base_folder, $this->index_script], ['', ''], $request_uri);
		if (empty($this->request_route)) {
			$this->request_route = "/";
		}

		// echo("[".$this->request_route."]");

    $this->doc_root = $_SERVER['DOCUMENT_ROOT'];
    $this->http_host = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    $this->register_routes();
  }

	function getDocRoot() {
		return $this->doc_root;
	}

  public function is_route_registered($route)
  {
    if ( ! empty($this->_routes_registered[$route])) {
      return true;
    }
    else {
      return false;
    }
  }

  public function register_route($route, $controller) {
    $this->_routes_registered[$route] = $controller;
  }

  public function register_routes() {
    $this->register_route('/', 'index');
    $this->register_route('/empleados_lista', 'empleados_lista');
    $this->register_route('/evaluaciones', 'evaluaciones');
    $this->register_route('/rotacion', 'rotacion');
    $this->register_route('/logout', 'logout');
  }
}
?>
<?php
namespace Router;

class Router  {
	public $base_folder = "/CFP18/tp_html_php";
	public $index_script = "/index.php";

  public $request_route = "";
  public $_routes_registered = [];

  /**
   *
   */
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

    $this->register_routes();
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
    $this->register_route('/empleados', 'empleados');
  }

}
?>
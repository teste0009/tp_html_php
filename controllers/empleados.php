<?php
namespace Controllers;
use \Models\Db;

class Empleados {
  private Db $db;


	function __construct() {
    $this->db = new Db();
	}

  public function getTotal() {
    $_empleadosTotal = $this->db->getFetchAllPaged('empleados_total', []);
    return $_empleadosTotal;
  }
}

?>
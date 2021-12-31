<?php
namespace Controllers;
use \Models\Db;


class Empleados {

  private Db $db;

	function __construct(Db &$db) {
    $this->db = $db;
	}

  public function getTotal() {
    $_empleadosTotal = $this->db->getFetchAllPaged('empleados_total', [], 2);
    return $_empleadosTotal;
  }
}

?>
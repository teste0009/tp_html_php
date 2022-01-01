<?php
namespace Controllers;
use \Models\Db;


class Empleados {

  private Db $db;
  private array $_fields_names_total = [
    'idx_num' => 'N&ordm;',
    'ciudad_d' => 'Ciudad Depto.',
    'area' => '&Aacute;rea',
    'departamento' => 'Departamento',
    'puesto' => 'Puesto',
    'estado' => 'Estado',
    'desde' => 'Desde',
    'hasta' => 'Hasta',
    'nro_legajo' => 'N&ordm; Legajo',
    'nombre' => 'Nombre',
    'dni' => 'DNI',
    'ciudad_e' => 'Ciudad',
  ];

	function __construct(Db &$db) {
    $this->db = $db;
	}

  private function getFieldsNamesTotal(): array {
		return $this->_fields_names_total;
	}

  public function getTotalPaged() {
    $_field_names = ['_field_names' => $this->getFieldsNamesTotal()];
    $_empleados_total_paged = $this->db->getFetchAllPaged('empleados_total', [], 2);
    return array_merge($_field_names, $_empleados_total_paged);
  }
}

?>
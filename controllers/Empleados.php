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

  private int $page_totalView = 0;

	function __construct(Db &$db) {
    $this->db = $db;
    $this->chkGet();
	}

  private function setPage_totalView(int $page_totalView): void {
		$this->page_totalView = $page_totalView;
	}

  public function getPage_totalView(): int {
		return $this->page_totalView;
	}

  private function getFieldsNamesTotal(): array {
		return $this->_fields_names_total;
	}

  private function chkGet() {
    if ( ! empty($_GET)) {
      if ( ! empty($_GET['page'])) { $this->setPage_totalView($_GET['page']); }
    }
     // show_between_pre_tag($_GET, "\$_GET");
  }

  public function getTotalPaged() {
    $_field_names = ['_field_names' => $this->getFieldsNamesTotal()];
    $_empleados_total_paged = $this->db->getFetchAllPaged('empleados_total', [], $this->getPage_totalView());
    return array_merge($_field_names, $_empleados_total_paged);
  }
}

?>

<?php
namespace Models;
use mysqli;

class Db {
  private array $_config = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'root',
    'database' => 'rrhh', // 'port' => '3306'
  ];

  private array $_sql = [
    'get_login'       => ["SELECT email, nombre FROM usuarios".
                            " WHERE email = ?".
                            " AND password = HEX(AES_ENCRYPT(?, UNHEX(SHA2('My secret passphrase',512)))) LIMIT 1;", 'ss'],
                            // SELECT AES_ENCRYPT('admin', UNHEX(SHA2('My secret passphrase',512))) => 0x1b287d7cfa9bad74fe30cbbc5dba2d05
                            // SELECT AES_DECRYPT(0x1b287d7cfa9bad74fe30cbbc5dba2d05, UNHEX(SHA2('My secret passphrase',512))) => admin
                            // SELECT HEX(0x1b287d7cfa9bad74fe30cbbc5dba2d05) => 1B287D7CFA9BAD74FE30CBBC5DBA2D05
                            // SELECT UNHEX('1B287D7CFA9BAD74FE30CBBC5DBA2D05') => 0x1b287d7cfa9bad74fe30cbbc5dba2d05
    'empleados_total' => ["SELECT cid.nombre AS ciudad_d, de.area, de.descripcion departamento, pu.nombre AS puesto,
                            st.descripcion AS estado, pe.desde, pe.hasta, em.nro_legajo, CONCAT(em.apellidos, ', ', em.nombres) AS nombre,
                            em.dni, cie.nombre AS ciudad_e

                            FROM empleados em

                            INNER JOIN ciudades cie ON (cie.id = em.id_ciudad)
                            LEFT JOIN puestos_empleados pe ON (pe.nro_legajo = em.nro_legajo)
                            LEFT JOIN departamentos de ON (de.codigo = pe.cod_departamento)
                            LEFT JOIN puestos pu ON (pu.id = pe.id_puesto)
                            LEFT JOIN estados st ON (st.codigo = pe.cod_estado)
                            LEFT JOIN ciudades cid ON (cid.id = de.id_ciudad)

                            WHERE ( (st.codigo = 100) OR (st.codigo >= 500) )
                            ORDER BY area, departamento, desde DESC", ''],
  ];

  private mysqli $mysqli;

  private int $rows_per_page = 10;

  function __construct() {
    mysqli_report(3); // MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $this->mysqli = new mysqli(...array_values($this->_config)); // echo('Class Db.<br/>'); // echo('MYSQLI_ASSOC = '.MYSQLI_ASSOC.'.<br/>');
  }

  function setRows_per_page(int $rows_per_page){
		$this->rows_per_page = $rows_per_page;
	}

  function getRows_per_page(): int {
		return $this->rows_per_page;
	}

  private function getStmtResult(array $_query, array $_params) {
    $stmt = $this->mysqli->prepare($_query[0]);
    if ( ! empty($_query[1])) {
      $stmt->bind_param($_query[1], ...array_values($_params));
    }
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getFetchOne(string $sql_ind, array $_params=[]): array {
    $_result = [];
    if ( ! empty($this->_sql[$sql_ind])) {
      $_stmt_result = $this->getStmtResult($this->_sql[$sql_ind], $_params);
      if ($_row = $_stmt_result->fetch_assoc()) {
        $_result = $_row;
      }
    }
    return $_result;
  }

  public function getFetchAll(string $sql_ind, array $_params=[]): array {
    $_result = [];
    if ( ! empty($this->_sql[$sql_ind])) {
      $_stmt_result = $this->getStmtResult($this->_sql[$sql_ind], $_params);
      while ($_row = $_stmt_result->fetch_assoc()) {
        $_result[] = $_row;
      }
    }
    return $_result;
  }

  private function getCountRows(string $sql_ind, array $_params=[]): int {
    $cantidad = 0;
    if ( ! empty($this->_sql[$sql_ind])) {
      $query = $this->_sql[$sql_ind][0];
      $param_types = $this->_sql[$sql_ind][1];
      // preg_match('/^(.*\R*)( from .*\R*)( order by .*\R*)$/ims', $query, $_matches); show_between_pre_tag($_matches, "\$_matches");
      $count_query = preg_replace('/^(.*\R*)( from .*\R*)( order by .*\R*)$/ims', 'SELECT COUNT(*) AS cantidad $2', $query); // show_between_pre_tag($count_query, "\$_count_query");
      $_stmt_result = $this->getStmtResult([$count_query, $param_types], $_params);
      if ($_row = $_stmt_result->fetch_assoc()) {
        $cantidad = $_row['cantidad'];
      }
    }
    return $cantidad;
  }

  private function getPagesQty(int $count_rows): int {
    return (int)ceil($count_rows / $this->rows_per_page);
  }

  private function getQueryPaged(string $sql_ind, array $_params=[], int $page=0): array {
    $_query_paged = [];

    $count_rows = $this->getCountRows($sql_ind, $_params); // echo("\$count_rows = ".$count_rows."<br/>");
    $pages_qty = $this->getPagesQty($count_rows); // echo("\$pages_qty = ".$pages_qty."<br/>"); var_dump($pages_qty); echo("<br/>");
    if ( ($page < 0) || ($page > ($pages_qty-1)) ) {
      $page = 0;
    }
    $str_query_paged = $this->_sql[$sql_ind][0] . " LIMIT " . ($page * $this->getRows_per_page()) . ", " . $this->getRows_per_page();
    $_query_paged = [
      'count_rows'    => $count_rows,
      'rows_per_page' => $this->getRows_per_page(),
      'pages_qty'     => $pages_qty,
      'page'          => $page,
      'query'         => $str_query_paged
    ];

    return $_query_paged;
  }

  public function getFetchAllPaged(string $sql_ind, array $_params=[], int $page=0): array {
    $_result = [];
    if ( ! empty($this->_sql[$sql_ind])) {
      $_query_paged = $this->getQueryPaged($sql_ind, $_params, $page); // show_between_pre_tag($str_query_paged, "\$str_query_paged");
      $_result = [
        'count_rows'    => $_query_paged['count_rows'],
        'rows_per_page' => $_query_paged['rows_per_page'],
        'pages_qty'     => $_query_paged['pages_qty'],
        'page'          => $_query_paged['page'],
      ];
      $_stmt_result = $this->getStmtResult([$_query_paged['query'], $this->_sql[$sql_ind][1]], $_params);
      $num_row = (int) $_result['page'] * $_result['rows_per_page'];
      while ($_row = $_stmt_result->fetch_assoc()) {
        $_result['_result'][$num_row] = $_row;
        $num_row++;
      }
    }
    return $_result;
  }

}

?>
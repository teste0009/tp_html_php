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
    'get_login' => ["SELECT email, nombre FROM usuarios".
                      " WHERE email = ?".
                      " AND password = HEX(AES_ENCRYPT(?, UNHEX(SHA2('My secret passphrase',512)))) LIMIT 1;", 'ss'],
                      // SELECT AES_ENCRYPT('admin', UNHEX(SHA2('My secret passphrase',512))) => 0x1b287d7cfa9bad74fe30cbbc5dba2d05
                      // SELECT AES_DECRYPT(0x1b287d7cfa9bad74fe30cbbc5dba2d05, UNHEX(SHA2('My secret passphrase',512))) => admin
                      // SELECT HEX(0x1b287d7cfa9bad74fe30cbbc5dba2d05) => 1B287D7CFA9BAD74FE30CBBC5DBA2D05
                      // SELECT UNHEX('1B287D7CFA9BAD74FE30CBBC5DBA2D05') => 0x1b287d7cfa9bad74fe30cbbc5dba2d05
];

  private $mysqli;

  function __construct() {
    mysqli_report(3); // MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $this->mysqli = new mysqli(...array_values($this->_config)); // echo('Class Db.<br/>'); // echo('MYSQLI_ASSOC = '.MYSQLI_ASSOC.'.<br/>');
  }

  private function getStmtResult(array $_query, array $_params) {
    $stmt = $this->mysqli->prepare($_query[0]);
    $stmt->bind_param($_query[1], ...array_values($_params));
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

}

?>
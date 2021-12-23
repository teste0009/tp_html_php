<?php
namespace Controllers;
use \Models\Db;

class User {
  private string $nombre = "";
  private string $email = "";
  private bool $bIsLogged = false;
  private Db $db;

  function __construct() {
    $this->db = new Db();
    $this->controller();
  }

  private function getSession() {
    // show_between_pre_tag($_SESSION, "\$_SESSION");
    if(isset($_SESSION['user'])) {
      if( ! empty($_SESSION['user']['name'])) {
        $this->setNombre($_SESSION['user']['name']);
        if( ! empty($_SESSION['user']['email'])) {
          $this->setEmail($_SESSION['user']['email']);
          $this->bIsLogged = true;
        }
      }
    }
  }

  public function getNombre(): string {
    return $this->nombre;
  }

  public function getEmail(): string {
    return $this->email;
  }

	public function setNombre(string $nombre) {
		$this->nombre = $nombre;
	}

  public function setEmail(string $email) {
		$this->email = $email;
	}

  public function isLogged(): bool {
    return $this->bIsLogged;
  }

  public function getLogin(string $email, string $password): array {
    return $this->db->getFetchOne('get_login', [$email, $password]);
  }

  private function controller() {
    $this->getSession();
  }
}

?>
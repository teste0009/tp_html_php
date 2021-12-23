<?php
namespace Controllers;
use \Models\Db;

class User {
  private string $nombre = "";
  private string $email = "";
  private bool $bIsLogged = false;
  private string $loginMessage = '';
  private Db $db;

  function __construct() {
    $this->db = new Db();
    $this->controller();
  }

  private function getSession() {
    if(isset($_SESSION['user'])) {
      if( ! empty($_SESSION['user']['nombre'])) {
        $this->setNombre($_SESSION['user']['nombre']);
        if( ! empty($_SESSION['user']['email'])) {
          $this->setEmail($_SESSION['user']['email']);
          $this->bIsLogged = true;
        }
      }
    }
    // show_between_pre_tag($_SESSION, "\$_SESSION");
  }

  private function setSession(array $_userLogin) {
    if ( ! empty($_userLogin)) {
      $_SESSION['user'] = [
        'nombre'  => $_userLogin['nombre'],
        'email' => $_userLogin['email']
      ];
      $this->setNombre ($_userLogin['nombre']);
      $this->setEmail  ($_userLogin['email']);
      $this->bIsLogged = true;
    }
    else {
      $this->setLoginMessage('Login incorrecto.');
    }
  }

  public function getNombre(): string {
    return $this->nombre;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function getLoginMessage(): string {
		return $this->loginMessage;
	}

	private function setNombre(string $nombre) {
		$this->nombre = $nombre;
	}

  private function setEmail(string $email) {
		$this->email = $email;
	}

  private function setLoginMessage(string $loginMessage){
		$this->loginMessage = $loginMessage;
	}

  public function isLogged(): bool {
    return $this->bIsLogged;
  }

  public function login(string $email, string $password) {
    $_userLogin = $this->db->getFetchOne('get_login', [$email, $password]);
    // show_between_pre_tag($_userLogin, "\$_userLogin");
    $this->setSession($_userLogin);
  }

  public function logout() {
    unset($_SESSION['user']);
    $this->setNombre ('');
    $this->setEmail  ('');
    $this->bIsLogged = false;
  }

  private function controller() {
    $this->getSession();
  }

}

?>
<?php
namespace Controllers;
use \Models\Db;

class User extends Db {
  private string $nombre = "";
  private string $email = "";
  private bool $bIsLogged = false;

  function __construct() {
    parent::__construct();
    echo 'Class User.<br/>';
  }

  function getNombre(): string {
    return $this->nombre;
  }

  function getEmail(): string {
    return $this->email;
  }

  function isLogged(): bool {
    return $this->bIsLogged;
  }
}

?>
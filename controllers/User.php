<?php
namespace Controllers;
use \Models\Db;

class User extends Db {


  function __construct() {
    parent::__construct();
    echo 'Class User.<br/>';
  }

}



?>
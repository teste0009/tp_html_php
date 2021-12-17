<?php
require '../rsc/requires.php';
// require '../router/Router.php';
// require '../controllers/App.php';

use Controllers\App;

$app = new App();
$app->response();
?>
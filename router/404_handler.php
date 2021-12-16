<?php
require '../rsc/requires.php';
require '../router/router.class.php';
require '../controllers/app.class.php';

use Controllers\App;

$app = new App();
$app->response();
?>
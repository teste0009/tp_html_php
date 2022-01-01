<?php
global $app;
use \Controllers\Empleados;

$app->title = 'Empleados';
$app->content_html = 'empleados_lista';

$empleados = new Empleados($app->db);
$_empleadosTotal = $empleados->getTotalPaged();

require(__DIR__.'/../'.'views/'.'layout'.'.html.php');

?>
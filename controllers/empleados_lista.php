<?php
global $app;
use \Controllers\Empleados;

$app->title = 'Empleados';
$app->content_html = 'empleados_lista';

$empleados = new Empleados($app->db);
$_empleadosTotal = $empleados->getTotalPaged();

require($app->getDocRoot().$app->base_folder.'/views/'.'layout'.'.html.php');

?>
<?php
global $app;

$app->title = 'Empleados';
$app->content_html = 'empleados_lista';

require(__DIR__.'/../'.'views/'.'layout'.'.html.php');

?>
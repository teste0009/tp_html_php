<?php
global $app;

echo("index");
show_between_pre_tag($app->request_route, "\$app->request_route");
$_usr = $app->user->getLogin('admin@email.com', 'admin');
show_between_pre_tag($_usr, "\$_usr");
$_usr = $app->user->getLogin('enzo@gmail.com', 'enzo');
show_between_pre_tag($_usr, "\$_usr");
$_usr = $app->user->getLogin('nico@gmail.com', 'nico');
show_between_pre_tag($_usr, "\$_usr");
?>
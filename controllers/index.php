<?php
global $app;

if ($app->user->isLogged) {
  $app->title = 'Home';
  $app->content_html = 'home';
}
else {
  $app->title = 'Login';
  $app->content_html = 'login';
}

require(__DIR__.'/../'.'views/'.'layout'.'.html.php');

/*
echo(__DIR__.'/../'.'views/content/'.$app->content_html.'.html.php'.'<br/>');
echo("index");
show_between_pre_tag($app->request_route, "\$app->request_route");
$_usr = $app->user->getLogin('admin@email.com', 'admin');
show_between_pre_tag($_usr, "\$_usr");
*/
?>
<?php
require_once '../lib/php_aux.php';
require_once '../router/router.class.php';
require_once '../controllers/app.class.php';

/*
echo($_SERVER['REDIRECT_URL']);

switch($_SERVER['REDIRECT_URL']) {
  case '/really_old_page.php':
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: /new-url/...');
  /*  As suggested in the comment, exit here.
      Additional output might not be handled well and
      provokes undefined behavior. */
  /*
      exit;
  default:
      header('HTTP/1.1 404 Not Found');
      die('404 - Not Found');
      ;
}
*/
?>
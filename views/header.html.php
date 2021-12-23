<?php
?>

  <header>
    <h1><?php echo($app->name); ?> - <?php echo($app->title); ?></h1>
    <nav>
<?php
  if ($app->user->isLogged) {
    foreach ($app->_nav_options as $arrKey => $_option) {
      $href = $app->http_host.$app->base_folder.$_option['route'];
      $name = $_option['name'];
?>
      <a href="<?php echo $href; ?>"><?php echo $name ?></a>
<?php
    }
  }
?>
    </nav>
  </header>

<?php
?>
<?php
global $app;

$app->user->logout();
header('Location: '.$app->base_folder.'/');
?>
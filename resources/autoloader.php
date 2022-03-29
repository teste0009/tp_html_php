<?php

// from: https://www.php.net/manual/en/language.namespaces.rationale.php#116280

function loadClass($className) {
  $fileName = '';
  $namespace = ''; // echo 'Into loadclass function.<br/>';

  // Sets the include path as the "src" directory
  $includePath = __DIR__.DIRECTORY_SEPARATOR.'..';

  if (false !== ($lastNsPos = strripos($className, '\\'))) {
    $namespace = strtolower(substr($className, 0, $lastNsPos));
    $className = substr($className, $lastNsPos + 1);
    $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
  $fullFileName = $includePath . DIRECTORY_SEPARATOR . $fileName; // echo $fullFileName.'<br/>';

  if (file_exists($fullFileName)) { // echo $fullFileName.' EXIST!<br/>';
    require $fullFileName;
  }
  else {
    echo 'Class "'.$className.'" does not exist.';
  }
}


spl_autoload_register('loadClass'); // echo 'Registers the autoloader.<br/>';

?>
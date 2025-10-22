<?php
declare(strict_types=1);

spl_autoload_register(function($class){
  $prefix = 'FaScrape\\';
  $base = __DIR__ . '/../src/';
  if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
  $rel = substr($class, strlen($prefix));
  $file = $base . str_replace('\\','/',$rel) . '.php';
  if (is_file($file)) require $file;
});

date_default_timezone_set('Europe/Dublin');

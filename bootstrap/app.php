<?php

/** 
 * initializa la sesion si no hay
 */
if ('' == session_id()) session_start();

/**
 * definir constantes para el funcionamiento
 */
define('ILUSTRO_BASE', dirname(__DIR__));
define('ILUSTRO_START', microtime(true));

/**
 * incluir el cargador de archivo
 */
$file = ILUSTRO_BASE . '/vendor/autoload.php';
if (!file_exists($file))
    require $file;
else
    require ILUSTRO_BASE . '/bootstrap/autoload.php';
$app = include ILUSTRO_BASE . '/bootstrap/initialize.php';
include ILUSTRO_BASE . '/bootstrap/broaden.php';
$app->dispatchAppliction();
<?php

include ILUSTRO_BASE . "/lib/src/loader/IBootloader.php";
include ILUSTRO_BASE . '/app/config.php';
include_once PEWE_DIR . "/helper.php";

/**
 * constante PEWE_DIR define el directorio base de la libreria
 */
IBootloader::start((new IRegister())->add(PEWE_DIR,[
    'Kowo\Ilustro' => ''
])->add(ILUSTRO_BASE, [
    'App' => '/app'
]));
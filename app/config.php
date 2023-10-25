<?php

$GLOBALS['config.web'] = [
    'route' => base_url('bootstrap/web/route.php'),
    'api' => base_url('bootstrap/web/api.php'),
];

$GLOBALS['config.app'] = [

    /**
     *	clave de la aplicacion para iniciar
    */
    'secret' => env('APP_KEY'),

    /**
     *	si la app esta en modo produccion o enviroment
    */
    'env' => env('ENVIRONMENT'),

    /**
     *	depurar envia los errores de la app que se han cometido
    */
    'debug' => env('DEBUG'),

    /**
     *	idioma de la aplicacion
    */
    'locale' => 'es',

    /**
     *	url del servidor
    */
    'url' => 'http://localhost',

    /**
     *	zona horaria || GMT
    */
    'timezone' => 'America/Bogota',

    /**
     * charset lang
     */
    'charset' => 'UTF-8',

    /**
     *	log de errores
    */
    'log' => env('LOG'),
    
    'fs_route' => true,
    
    'path_pages' => base_url('app/pages'),
];

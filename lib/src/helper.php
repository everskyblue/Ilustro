<?php
/**
 * definir esta variable con $GLOBALS para que no ocurra un error
 * al llamar el autoload de composer, no renoce la definicion de la variable
 * $ini = parse_ini_file(ILUSTRO_BASE . '/env.ini'); retorna un valor nulo
 * dentro la las funcion declarada
 */
$GLOBALS['ini'] = parse_ini_file(ILUSTRO_BASE . '/env.ini');

if (!function_exists('config')) {
    function config($key) {
        $ini = $GLOBALS;
        $data = explode('.', $key);
        $x = 0;
        foreach($data as $key_array){
            if (!$x) {
                $key_array = 'config.'.$key_array;
                $x = 1;
            }
            if(array_key_exists($key_array, $ini)){
                $ini = $ini[$key_array];
            }else{
                throw new \Exception("key {$key_array} not exits");
            }
        }
        return $ini;
    }
}


if (!function_exists('env')) {
    function env($key) {
        global $ini;
        return $ini[$key];
    }
}


if (!function_exists('base_url')) {
    function base_url($url = '') {
        return ILUSTRO_BASE . DIRECTORY_SEPARATOR . $url;
    }
}

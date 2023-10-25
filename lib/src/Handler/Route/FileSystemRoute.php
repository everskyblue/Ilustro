<?php

namespace Ilustro\Handler\Route;

class FileSystemRoute {
    
    public static function getHandlerRoute(string $base, string $url): string | bool {
        // divide la url
        $split_url = array_filter(explode('/', $url));
        // obtiene el primer argumento de la url
        $first_url = array_shift($split_url);
        // escanea el directorio base
        $scanfs = scandir($base);
        /**
         * verifica si existe el nombre del archivo o directorio en la ruta
         * si existe lo concatena
         */
        if (in_array($first_url, $scanfs) || in_array($first_url . '.php', $scanfs)) {
            $base = trim($base) . '/' . $first_url;
        }
        // verifica si no hay mas argumentos en la url
        if (count($split_url) === 0) {
            /**
             * comprueba que se haya añadido el arg de la url a la url base
             * retorna la ruta del archivo
             */
            if (str_ends_with($base, $first_url)) {
                if (is_dir($base)) return $base . substr($base, strrpos($base, '/')) . '.php';
                if (is_file($base . '.php')) return $base . '.php';
            }
            
            // encuentra si existe un archivo con parametros
            foreach ($scanfs as $i => $fs) {
                if (str_starts_with($fs, '[')) {
                    return $base . '/' . $fs;
                }
            }
            return false;
        }
        
        if (count($split_url) > 0) {
            return static::getHandlerRoute($base, join('/', $split_url));
        }
        
        return false;
    }
    
    public static function match(string $base, string $url): string | bool {
        return static::getHandlerRoute($base, $url);
    }
    
}
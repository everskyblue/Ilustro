<?php

define("PEWE_DIR", dirname(dirname(__FILE__)));

include "IRegister.php";

$fr = __DIR__ . DIRECTORY_SEPARATOR . "register.json";

if (!file_exists($fr)) {
   file_put_contents($fr, '[]');
}

/**
 * @description cargador de clases
 * @package IBootloader
 **/
class IBootloader {

    /**
     * @var IRegister
     */
    private static $ns;

    /**
     * @var array registro de archivos cargados
     */
    private static $register = [],

    /**
     * @var array añade los archivos incluidos
     */
    $included = [];

    /**
     * @access private
     */
    private static function readRegister()
    {
        global $fr;

        if (file_exists($fr)) {
            //self::$register = json_decode(file_get_contents($fr), true);
        }
    }

    /**
     * @param string $file
     */
    private static function appendRegister($file)
    {
        global $fr;
        array_push(self::$register, $file);
        $content = json_encode(array_reverse(self::$register));
        $h = fopen($fr, "w");
        fwrite($h, $content);
        fclose($h);
    }

    /**
     * @param IRegister $ns
     */
    public static function start($ns)
    {
        self::$ns = $ns;
        self::readRegister();
        spl_autoload_register(__CLASS__. '::pipe');
    }

    /**
     * @param string $cl nombre de la clase
     */
    public static function pipe($cl)
    {
        $pl = explode("\\", $cl);
        $cl_name = array_pop($pl);
        $path = dirname(self::$ns->resolvePath($cl));
        $file = $path . DIRECTORY_SEPARATOR . $cl_name . ".php";
        set_include_path($path);
        if (is_file($file)) {
            if (!in_array($file, self::$register))
                self::appendRegister($file);
            foreach(self::$register as $f) {
                if (!in_array($f, self::$included)){
                    array_push(self::$included, $f);
                    require $f;
                }
            }
        } else {
            throw new \Exception("class {$cl_name} {$file} not fount");
        }
    }
}
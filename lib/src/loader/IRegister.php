<?php

/**
 * @description registar namespace
 * @package IRegister
 */
class IRegister {

    /**
     * @var array
     */
    protected $namespace = [];
    
    /**
     * @description añade namespace de las clase a incluir
     * @param string $dir directorio base
     * @param array $ns registro de namespace
     * @return $this
     */
    public function add($dir, array $ns)
    {
        $this->base_dir = $dir;
        $this->namespace = array_merge($this->namespace, $ns);
        return $this;
    }
    
    /**
     * @description se pasa como parametro el nombre
     * de la clase que se esta llamando y se reemplaza
     * por la ruta del archivo
     *
     * @param string $name nombre de la clase
     * @return string
     */
    public function __get($name)
    {
        $rp = ['/', '\\'];
        foreach($this->namespace as $ns => $dir) {
            if (strpos($name, $ns)!==false||$name===$ns){
                $path = str_replace($ns, $dir, $name);
                return $this->absPath($rp, $path);
            } 
        }
        return $this->absPath($rp, $name);
    }
    
    /**
     * @param array|string $m
     * @param string $rp
     * @return string
     */
    private function absPath($m, $rp)
    {
        return $this->base_dir . str_replace($m, DIRECTORY_SEPARATOR, $rp);
    }
}
<?php

namespace Ilustro\Http\Url;


class Builder {
    
    /**
     * añadir parametros a la url
     *
     * @param string $url
     * @param array $builder
     * @return string
     */
    public function appendParamsUrlBase(array $builder = [])
    {
        $base = $this->url;
        if(!$builder){
            return $base;
        }
        if(strpos($base, '?') === false){
          return $base . '?' . http_build_query($builder, null ,'&');
        }
        list($path, $query) = explode('?', $base, 2);
        $extP = [];
        parse_str($query, $extP);
        //une el query
        $builder = array_merge($builder, $extP);
        //ordena el query
        ksort($builder);
        return $path . '?' . http_build_query($builder, null , '&');
    }
}
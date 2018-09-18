<?php

namespace Kowo\Ilustro\Request\Http;

trait DataServer
{
    /**
     * @var array añade los parametros de la cookie
     */
    protected $cookie = [];

    /**
     * @var array añade parametros al stream php
     */
    protected $streamInput = [];

    /**
     * @var  null|object|array obtiene stream input
     */
    protected $body;

    /**
     * parametros enviado por get
     *
     * @access public
     * @param null $key
     * @return mixed
     */
    public function getQueryParams($key = null)
    {
        $query = [];

        parse_str($this->getVar('QUERY_STRING'), $query);

        return $key !== null  ? (isset($query[$key]) ? $query[$key] : null) : $query;
    }

    /**
     * obtiene los valores enviados por el form y añade los valores en array
     *
     * @access protected
     * @param null $name
     * @return array|string
     */
    protected function streamInput($name = null)
    {
        $content = file_get_contents('php://input');

        if(!empty($content) && empty($this->streamInput)){
            parse_str($content, $this->streamInput);
        }elseif (!empty($_POST)) {
            parse_str(urldecode(http_build_query($this->getPostParams())), $this->streamInput);
        }

        if (isset($this->streamInput['_method'])) {
            unset($this->streamInput['_method']);
        }

        return (is_null($name) ? $this->streamInput : (isset($this->streamInput[$name]) ? $this->streamInput[$name] : null));
    }

    /**
     * @access public
     * @param null $name
     * @return array
     */
    public function body($name = null)
    {
        return $this->streamInput($name);
    }

    /**
     * content type enviados o aceptados
     *
     * @access public
     * @return array
     */
    public function getAccept()
    {
        $accept = $this->getHeader('ACCEPT');
        $divide = explode(';', $accept);
        $content = array_shift($divide);
        if(is_string($content)){
            $getType = explode(',', $content);
        }
        $qv = is_array($divide) ? array_shift($divide) : '';
        if(strpos($qv, ',') !== false){
            $q = explode(',', $qv);
        }
        $qv = isset($q) ? array_merge($q, $divide) : [];

        return array_merge($getType, $qv);
    }

    /**
     * @access public
     * @return array|string
     */
    public function getBodyParams()
    {
        $type = $this->getVar('CONTENT_TYPE');
        $split = explode(' ', $type);
        $type = isset($split[0]) ? $split[0] : $type;
        switch(str_replace(';','',$type)){
            case 'multipart/form-data':
            case 'application/x-www-form-urlencoded':
                $this->body = $this->streamInput();
                break;
            case 'text/json':
            case 'application/json':
                $this->body = json_encode($this->streamInput());
                break;
        }
        return $this->body;
    }

    /**
     * @access public
     * @return array
     */
    public function getUploadedFiles()
    {
        return $_FILES;
    }

    /**
     * @access public
     * @return array
     */
    public function getCookieParams()
    {
        return $_COOKIE;
    }

    /**
     * @access public
     * @return array
     */
    public function getServerParams()
    {
        return $_SERVER;
    }

    /**
     * @access public
     * @return array
     */
    public function getPostParams()
    {
        return $_POST;
    }
    
    /**
     * @access public
     * @param string $key
     * @return mixed
     */
    public function getPostParam($key)
    {
        $post = $this->getPostParams();
        return isset($post[$key]) ? $post[$key] : null;
    }
    
    /**
     * obtien el path de la url removiendo el script que se esta ejecutando
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        $request = $this->getVar('REQUEST_URI');
        if(strpos($request, '/') !== false){
            $script = strrchr($this->getVar('SCRIPT_NAME'), '/');
            if(0 === substr_compare($request, $script, 0, strlen($script))){
                $request = substr($request, strlen($script));
            }
        }
        return $request ? $request : '/';
    }

    /**
     * obtiene las cookie principales enviadas por php
     *
     * @access public
     * @return array
     */
    public function getCookie()
    {
        $cookie = $this->getHeader('COOKIE');
        $cookie = str_replace(';', '&', $cookie);
        if(!empty($cookie)){
            parse_str($cookie, $this->cookie);
        }

        $trackid = isset($this->cookie['TRACKID']) ? ['TRACKID' => $this->cookie['TRACKID']] : [];

        $phpsessid = isset($this->cookie['PHPSESSID']) ? ['PHPSESSID' => $this->cookie['PHPSESSID']] : [];

        return $trackid + $phpsessid;
    }

    /**
     * @access public
     * @return array
     */
    public function getAllCookie()
    {
        return  $this->cookie;
    }

    /**
     * obtiene la version del protocol que se esta ejecutando
     *
     * @access public
     * @return float|string
     */
    public function getVersionProtocol()
    {
        $protocol = $this->getVar('SERVER_PROTOCOL');
        if($int = strpos($protocol, '/')){
            $version = substr($protocol, $int + 1);
        }
        return isset($version) ? (float)$version : $protocol;
    }

    /**
     * protocol http | https
     *
     * @access public
     * @return string
     */
    public function getProtocol()
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    /**
     * @access public
     * @return string numero del puerto
     */
    public function getPort()
    {
        return $this->getVar('SERVER_PORT');
    }

    /**
     * obtiene el host con el puerto
     *
     * @access public
     * @return string
     */
    public function getNameHost()
    {
        $ptl = $this->getProtocol();
        $port = $this->getPort();
        switch($ptl){
            case 'http':
                if($port === '80') {
                    $port = '80';
                }
                $port = (int)$port;
                break;
            case 'https':
                $port = $port === '443' ? 433 : (int)$port;
                break;
        }
        return $ssl . '://'. $this->getHost() . ':' . $port;
    }

    /**
     * obtiene nombre del host removiendo el puerto si existe
     *
     * @access public
     * @return string
     */
    public function getHost()
    {
        $host = $this->getHeader('HOST');
        if($length = strpos($host, ':')){
            $host = substr($host, 0, $length);
        }
        return (string)$host;
    }

    /**
     * @access protected
     * @return string
     */
    protected function isSsl()
    {
        return $this->getHeader('HTTPS') ? true : false;
    }
    
    /**
     * @return bool
     */
    public function isAjax()
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }
    
    /**
     * @access protected
     * @param string $key
     * @return null|string
     */
    protected function getVar($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    /**
     * @access public
     * @param string $key
     * @return null|string
     */
    public function getHeader($key)
    {
        return $this->getVar('HTTP_'.$key);
    }
}
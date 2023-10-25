<?php

namespace Ilustro\Http\Request;

trait Data
{
    protected array $cookie = [];

    protected array $streamInput = [];

    protected mixed $body;

    /**
     * parametros enviado por get
     */
    public function getQueryParams(string $key = null): mixed
    {
        $query = [];

        parse_str($this->getVar('QUERY_STRING'), $query);

        return $key !== null  ? (isset($query[$key]) ? $query[$key] : null) : $query;
    }

    /**
     * obtiene los valores enviados por el form y añade los valores en array
     */
    protected function streamInput(string $name = null): array | string
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

    public function body(string $name = null): mixed
    {
        return $this->streamInput($name);
    }

    public function getAccept(): array
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

    public function getBodyParams(): array | string
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

    public function getUploadedFiles(): array
    {
        return $_FILES;
    }

    public function getCookieParams(): array
    {
        return $_COOKIE;
    }

    public function getServerParams(): array
    {
        return $_SERVER;
    }

    public function getPostParams(): array
    {
        return $_POST;
    }

    public function getPostParam(string $key): mixed
    {
        $post = $this->getPostParams();
        return isset($post[$key]) ? $post[$key] : null;
    }

    public function getPath(): string
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

    public function getCookie(): array
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

    public function getAllCookie(): array
    {
        return  $this->cookie;
    }

    public function getVersionProtocol(): string
    {
        $protocol = $this->getVar('SERVER_PROTOCOL');
        if($int = strpos($protocol, '/')){
            $version = substr($protocol, $int + 1);
        }
        return isset($version) ? $version : $protocol;
    }

    public function getProtocol(): string
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    public function getPort(): string
    {
        return $this->getVar('SERVER_PORT');
    }

    public function getNameHost(): string
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

    public function getHost(): string
    {
        $host = $this->getHeader('HOST');
        if($length = strpos($host, ':')){
            $host = substr($host, 0, $length);
        }
        return (string)$host;
    }

    protected function isSsl(): bool
    {
        return $this->getHeader('HTTPS') ? true : false;
    }

    public function isAjax(): bool
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    protected function getVar(string $key): mixed
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public function getHeader(string $key): mixed
    {
        return $this->getVar('HTTP_'.$key);
    }
}
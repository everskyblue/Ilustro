<?php

namespace Kowo\Ilustro\Http;


use Kowo\Ilustro\Wrapper\Capsule;
use Kowo\Ilustro\Http\Response\TMsgCode;


class Response {

    use TMsgCode;

    /**
     * @var Capsule
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $output_content = '';

    /**
     * @var string
     */
    protected $content_type = [
        'text/html',
        'text/xml',
        'text/plain',
        'text/json',
        'application/json',
        'application/pdf',
        'Content-Disposition: attachment; filename=filename',
    ];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param Request $request
     */
    public function __construct(Request $request, Capsule $container = null)
    {
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * add header
     * @param string $key
     * @param string $val
     * @return $this
     */
    public function withHeader($key, $val)
    {
        $this->headers[$key] = $val;
        return $this;
    }

    public function getHeaderLine($key)
    {
        $list = headers_list();

        return isset($list[$key]) ? $list[$key] : null;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function withStatus($code)
    {
        if(!array_key_exists($code, $this->msg_code)){
            throw new \RuntimeException("code status not valid");
        }

        $this->status = $code;

        return $this;
    }

    public function signedCookie($name, $value, $time, array $opt = null)
    {
        setcookie($name, $value, $time, $opt);
    }

    public function getStatusMsgHeader()
    {
        return $this->status .' '. $this->msg_code[$this->status];
    }

    /**
     * dispatcher function and send header
     */
    public function send()
    {
        $request = $this->request;

        $http = strtoupper($request->getProtocol()) . '/' . $request->getVersionProtocol();

        $header = [
            $request->getMethod() .' '. $request->getPath() .' '. $http,
            $http .' '. $this->getStatusMsgHeader()
        ];

        if (!isset($this->headers['Content-Type'])) {
            $this->withHeader('Content-Type', 'text/html');
        }

        if ($this->dispatcher) {
            list($route, $action, $params) = $this->dispatcher;
            $this->setContentView($route->invokeAction($action, $params, $this->container));
        }

        if (count($this->headers) > 0) {
            foreach($this->headers as $type => $value) {
                $header[] = $type .':'. $value;
            }
        }

        $this->toHeader($header);

        $this->outputContent();
    }

    /**
     * @param array $header
     */
    private function toHeader(array $header)
    {
        if (!headers_sent()) {
            foreach($header as $send_header) {
                header($send_header);
            }
        }
    }

    /**
     * @param mixed $rv
     */
    protected function setContentView($rv)
    {
        if (is_object($rv)) {
            ob_start();
            var_dump($rv);
            $content = ob_get_contents();
            ob_clean();
        } elseif (is_array($rv)) {
            if (isset($rv['_method'])) unset($rv['_method']);

            $this->withHeader('Content-Type', 'text/json');

            $content = json_encode($rv);
        } else {
            $content = $rv;
        }

        $this->output_content = $content;

        $this->withHeader('Content-Length', strlen((string)$content));
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status >= 200 && $this->status <= 207;
    }

    /**
     * redirect url before
     * @param int $code
     * @return $this
     */
    public function back($code = 301)
    {
        $this->withStatus($code);

        $this->withHeader('Location', $this->request->getHeader('REFERER'));

        return $this;
    }

    /**
     * @param string $redirect
     * @return $this
     */
    public function redirect($redirect)
    {
        if($this->status == null) $this->withStatus(307);
        $this->withHeader('Location', $redirect);
        return $this;
    }

    protected function outputContent()
    {
        if (!empty($this->output_content)) {
            $open = fopen("php://output", "w");
            fputs($open, $this->output_content);
            fclose($open);
        }
    }

    /**
     * @param string|callable $func
     * @param array $params
     * @param int $status
     */
    public function setDispatcher($route, $func, $params, $status)
    {
        $this->dispatcher = [$route, $func, $params];

        return $this->withStatus($status);
    }

    /**
     * @return array
     */
    public function getWithHeaders()
    {
        return $this->headers;
    }
}
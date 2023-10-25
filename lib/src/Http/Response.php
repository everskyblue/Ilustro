<?php

namespace Ilustro\Http;

use Ilustro\Wrapper\Capsule;
use Ilustro\Http\Response\TMsgCode;

class Response
{
    use TMsgCode;

    protected Capsule $container;

    protected Request $request;

    protected int $status = 0;

    protected mixed $content;

    protected array $content_type = [
        "text/html",
        "text/xml",
        "text/plain",
        "text/json",
        "application/json",
        "application/pdf",
        "Content-Disposition: attachment; filename=filename",
    ];

    protected array $headers = [];

    public function __construct(Request $request, Capsule $container = null)
    {
        $this->request = $request;
        $this->container = $container;
    }

    public function withHeader(string $key, string $val): self
    {
        $this->headers[$key] = $val;
        return $this;
    }

    public function getHeaderLine(string $key): mixed
    {
        $list = headers_list();

        return isset($list[$key]) ? $list[$key] : null;
    }

    public function withStatus(int $code): self
    {
        if (!array_key_exists($code, $this->msg_code)) {
            throw new \RuntimeException("code status not valid");
        }

        $this->status = $code;

        return $this;
    }

    public function withContent(mixed $content): self
    {
        if (is_object($content)) {
            ob_start();
            $content = ob_get_contents();
            ob_clean();
        } elseif (is_array($content)) {
            if (isset($content["_method"])) {
                unset($content["_method"]);
            }

            $this->withHeader("Content-Type", "text/json");

            $content = json_encode($rv);
        }

        $this->content = $content;

        $this->withHeader("Content-Length", strlen((string) $content));

        return $this;
    }

    public function signedCookie(string $name, string $value, int $time, array $opt = null)
    {
        setcookie($name, $value, $time, $opt);
    }

    public function getStatusMsgHeader(): string
    {
        return $this->status . " " . $this->msg_code[$this->status];
    }

    public function send()
    {
        $request = $this->request;

        $http =
            strtoupper($request->getProtocol()) .
            "/" .
            $request->getVersionProtocol();

        $header = [
            $request->getMethod() . " " . $request->getPath() . " " . $http,
            $http . " " . $this->getStatusMsgHeader(),
        ];

        if (!isset($this->headers["Content-Type"])) {
            $this->withHeader("Content-Type", "text/html");
        }

        if (count($this->headers) > 0) {
            foreach ($this->headers as $type => $value) {
                $header[] = $type . ":" . $value;
            }
        }

        $this->sendHeaders($header)->sendContent();
    }

    /**
     * @param array $header
     */
    private function sendHeaders(array $header): self
    {
        if (!headers_sent()) {
            foreach ($header as $send_header) {
                header($send_header);
            }
        }
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->status >= 200 && $this->status <= 207;
    }

    public function back(int $code = 301): self
    {
        return $this->withStatus($code)->withHeader("Location", $this->request->getHeader("REFERER"));
    }

    public function redirect(string $redirect): self
    {
        if ($this->status == null) {
            $this->withStatus(307);
        }
        return $this->withHeader("Location", $redirect);
    }

    protected function sendContent()
    {
        if (!empty($this->content)) {
            $open = fopen("php://output", "w");
            fputs($open, $this->content);
            fclose($open);
        }
    }

    public function getWithHeaders(): array
    {
        return $this->headers;
    }
}

<?php

namespace Kowo\Ilustro\Handler\Bug;


use Kowo\Ilustro\Html\Tag;
use Throwable;

if (!defined('PATH_ICON')) {
    define('PATH_ICON', 
        ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://') .
        $_SERVER['SERVER_NAME'] . 
        '/public/assets/image/framework/'
    );
}

/**
 * @package Kowo\Ilustro\Handler\Bug
 */
class Mistake {

    use MistakeBodyTrait, MistakeRenderTrait;

    /**
     * @var MistakeConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $entry = [];

    /**
     * @param MistakeConfig $mc
     */
    public function __construct(MistakeConfig $mc)
    {
        $this->config = $mc;
        $this->configMenu();
        $this->configHighlight();
    }

    protected function configHighlight()
    {
        ini_set('highlight.string', '#F8F8F8');
        ini_set('highlight.default', '#c5d311');
        ini_set('highlight.keyword', '#2affb1');
        ini_set('highlight.html', '#903c86');
    }

    /**
     * @return $this
     */
    public function setError(): self
    {
        set_error_handler([$this, 'errTpl']);
        return $this;
    }

    /**
     * @param int $errno
     * @param string $msg
     * @param string $errfile
     * @param int $errline
     */
    public function errTpl(int $errno, string $msg, string $errfile, int $errline)
    {
        $this->message('HUBO UN ERROR INTERNO', $msg, $errno);
        $this->entry = [$errfile, $errline, $errfile];
        $this->commit();
    }

    /**
     * @return $this
     */
    public function setException(): self
    {
        set_exception_handler([$this, 'exTpl']);
        return $this;
    }

    /**
     * @param Exception $e
     */
    public function exTpl($e)
    {
        $this->message('Exception: '. get_class($e), $e->getMessage(), $e->getCode());
        $this->output(
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
    }

    /**
     * añade una excepcion
     *
     * @param \Throwable $th
     * @return void
     */
    public function on(\Throwable $th)
    {

    }

    public function commit()
    {
        $this->output(...$this->entry);
    }

    /**
     * @return $this
     */
    public function setHandler()
    {
        $this->setError();
        $this->setException();
        return $this;
    }
}
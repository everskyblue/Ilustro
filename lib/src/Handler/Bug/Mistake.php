<?php

namespace Kowo\Ilustro\Handler\Bug;


use Kowo\Ilustro\Html\Tag;


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
    }

    /**
     * @return $this
     */
    public function reportError()
    {
        set_error_handler([$this, 'errTpl']);
        return $this;
    }

    /**
     * @param int $errno
     * @param syring $msg
     * @param string $errfile
     * @param int $errline
     */
    public function errTpl($errno, $msg, $errfile, $errline)
    {
        $this->message('HUBO UN ERROR INTERNO', $msg, $errno);
        $this->output($errfile, $errline, $errfile);
    }

    /**
     * @return $this
     */
    public function reportException()
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
     * @return $this
     */
    public function report()
    {
        $this->reportError();
        $this->reportException();
        return $this;
    }
}
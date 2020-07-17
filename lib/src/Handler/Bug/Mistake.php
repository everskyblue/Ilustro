<?php

namespace Kowo\Ilustro\Handler\Bug;


use Kowo\Ilustro\Html\Tag;
use Throwable, ErrorException;

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
     * @var string
     */
    public const ON_ERROR = 'error';

    /**
     * @var string
     */
    public const ON_EXCEPTION = 'exception';

    /**
     * @var MistakeConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $events = [];

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
        set_error_handler(function (int $errno, string $msg, string $errfile, int $errline) {
            if (!in_array($errno, $this->config->exclude_type_errors) && (error_reporting() & $errno)) {
                throw new ErrorException($msg, 0, $errno, $errfile, $errline);
            }
        });
        return $this;
    }

    /**
     * @return $this
     */
    public function setException(): self
    {
        set_exception_handler([$this, 'commit']);
        return $this;
    }

    /**
     * @param Throwable $e
     */
    public function commit(Throwable $e)
    {
        $this->message('Exception: '. get_class($e), $e->getMessage(), $e->getCode());
        // try {
            $this->output(
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        /* } catch (\Throwable $th) {
            var_dump($th->getMessage());
        } */
        
    }

    /**
     * añade una excepcion
     *
     * @param callable $fn
     * @return void
     */
    public function on(callable $fn)
    {
        $this->events[] = $fn;
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
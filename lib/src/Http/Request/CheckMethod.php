<?php

namespace Ilustro\Http\Request;

trait CheckMethod {

    /**
     * obtiene el nombre del metodo del input hidden
     */
    protected function getInputMethod(): mixed
    {
        return $this->getPostParam('_method');
    }

    /**
     * check method http
     */
    public function compareMethod(string $method): bool
    {
        return in_array($this->getInputMethod() ?: $this->getMethod(), explode(',', $method));
    }

    public function getMethod(): string
    {
        return $this->getVar('REQUEST_METHOD');
    }

    public function isPost(string $method): bool
    {
        return $this->getMethod() === $method && !$this->getInputMethod();
    }

    public function isGet(string $method): bool
    {
        return $this->getMethod() === $method;
    }

    public function isPut(string $method): bool
    {
        return $this->getInputMethod() === $method;
    }

    public function isPatch(string $method): bool
    {
        return $this->getInputMethod() === $method;
    }

    public function isDelete(string $method): bool
    {
        return $this->getInputMethod() === $method;
    }

    public function isHead(string $method): bool
    {
        return $this->getInputMethod() === $method;
    }

    public function getAllMethod(): array
    {
        return ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    }

    public function isOptions(string $method): bool
    {
        return $this->getInputMethod() === $method;
    }
}
<?php

namespace Kowo\Ilustro\Http\Request;

trait CheckMethod {

    /**
     * obtiene el nombre del metodo del input hidden
     *
     * @access protected
     * @return string|null
     */
    protected function getInputMethod()
    {
        return $this->getPostParam('_method');
    }

    /**
     * check method http
     *
     * @param string $s
     * @return bool
     */
    public function compareMethod($s)
    {
        $c = $this->getInputMethod() ?: $this->getMethod();
        if (in_array($c, explode(',', $s))) {
            return true;
        }
        return false;
    }

    /**
     * @access public
     * @return string
     */
    public function getMethod()
    {
        return $this->getVar('REQUEST_METHOD');
    }

    /**
     * @access public
     * @param $method
     * @return bool
     */
    public function isPost($method)
    {
        return $this->getMethod() === $method && !$this->getInputMethod();
    }

    /**
     * @access public
     * @param $method
     * @return bool
     */
    public function isGet($method)
    {
        return $this->getMethod() === $method;
    }

    /**
     * @access public
     * @param string $method
     * @return bool
     */
    public function isPut($method)
    {
        return $this->getInputMethod() === $method;
    }

    /**
     * @access public
     * @param string $method
     * @return bool
     */
    public function isPatch($method)
    {
        return $this->getInputMethod() === $method;
    }

    /**
     * @access public
     * @param string $method
     * @return bool
     */
    public function isDelete($method)
    {
        return $this->getInputMethod() === $method;
    }

    /**
     * @access public
     * @param string $method
     * @return bool
     */
    public function isHead($method)
    {
        return $this->getInputMethod() === $method;
    }

    /**
     * todos los metodos permitidos
     *
     * @access protected
     * @return array
     */
    public function getAllMethod()
    {
        return ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    }

    /**
     * @access public
     * @param string $method
     * @return bool
     */
    public function isOptions($method)
    {
        return $this->getInputMethod() === $method;
    }
}
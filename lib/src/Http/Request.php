<?php

namespace Ilustro\Http;


use lustro\Http\Url\Component as ComponentUrl;

class Request {

    use Request\Data, Request\CheckMethod;

    /**
     * @var ComponentUrl|null
     */
    protected $url;

    /**
     * @param ComponentUrl|null $url
     */
    public function __construct(ComponentUrl $url = null)
    {
        $this->url = $url;
    }

    /**
     * @param ComponentUrl $url
     */
    public function setUrl(ComponentUrl $cu)
    {
        $this->url = $cu;
    }
}
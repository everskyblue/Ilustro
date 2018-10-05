<?php

namespace Kowo\Ilustro\Http\Url;


class Component extends Builder
{
    	/**
    	 * @var string
    	 */
    protected $url;
    	
    	/**
    	 * @param string $url 
    	 */
    public function __construct($url = '')
    {
    		  $this->url = $url;
    }
    	
    	/**
    	 * @param string $url 
    	 */
    public function setUrl($url)
    {
    		  $this->url = $url;
    }
    	
    	/**
    	 * @access public
    	 * @return array
    	 */
    public function getScheme()
    {
    		  return parse_url($this->url);
    }
    	 
    	/**
    	 * @access public
    	 * @return string
    	 */
    public function getPath()
    {
    		  return parse_url($this->url, PHP_URL_PATH);
    }
    	
    	/**
    	 * @access public
    	 * @return string
    	 */
    public function getHost()
    {
    		  return parse_url($this->url, PHP_URL_HOST);
    }
    	
    	/**
    	 * @access public
    	 * @return array|null
    	 */
    public function getUserInfo()
    {
  		     $info = $this->getScheme();
      		if(isset($info['user']) && isset($info['pass'])){
      			  return [$info['user'], $info['pass']];
      		}
      		
      		return null;
    }
    	
    	/**
    	 * @access public
    	 * @return int
    	 */
    public function getPort()
    {
    		  return parse_url($this->url, PHP_URL_PORT);
    }
    
    	/**
    	 * @access public
    	 * @return string
    	 */ 
    public function getQuery()
    {
    		  return parse_url($this->url, PHP_URL_QUERY);
    }
}
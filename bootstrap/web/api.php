<?php

$route->group('/api', function ($route) {
    $route->get('', 'Api\ApiController@index');
});
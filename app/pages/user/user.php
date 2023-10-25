<?php

use Ilustro\Http\Response;

$user = static function($request, Response $response) {
    return 'hola user';
};


return $user;
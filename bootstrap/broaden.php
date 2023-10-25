<?php

/**
 * registrar nuevos elementos a la aplicacion
 */

$app->container->register('view', function() {
    return new \Twig\Environment(new \Twig\Loader\FilesystemLoader(ILUSTRO_BASE . '/static/views'), [
        'debug' => true,
        'strict_variables' => true,
        'cache' => ILUSTRO_BASE . '/.cache/views'
    ]);
});


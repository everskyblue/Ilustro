<?php

use Ilustro\Ilustro;
use Ilustro\Handler\Bug\Mistake;
use Ilustro\Handler\Bug\MistakeConfig;

$app = Ilustro::create(new Mistake(new MistakeConfig()));

$app->dispatchConfigApplication();

return $app;
<?php

use Kowo\Ilustro\Ilustro;
use Kowo\Ilustro\Handler\Bug\Mistake;
use Kowo\Ilustro\Handler\Bug\MistakeConfig;

$app = Ilustro::create(new Mistake(new MistakeConfig()));

$app->dispatchConfigApplication();

return $app;
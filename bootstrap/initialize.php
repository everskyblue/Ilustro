<?php

use Kowo\Ilustro\Ilustro;
use Kowo\Ilustro\Handler\Bug\Mistake;
use Kowo\Ilustro\Handler\Bug\MistakeConfig;

$ms = new Mistake(new MistakeConfig());
$ms->initializeContent();
$ms->report();

$app = Ilustro::create();

$app->dispatchConfigApplication();

return $app;
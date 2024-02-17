<?php

require __DIR__.'/../vendor/autoload.php';

$container = require __DIR__.'/rector-bootstrap.php';

return $container->get('doctrine')->getManager();

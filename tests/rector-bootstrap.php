<?php

use App\Kernel;

require __DIR__.'/bootstrap.php';

$kernel = new Kernel('test', false);
$kernel->boot();

return $kernel->getContainer();

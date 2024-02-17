<?php

use FriendsOfTwig\Twigcs\Config\Config;
use FriendsOfTwig\Twigcs\Finder\TemplateFinder;

$finder = TemplateFinder::create()
    ->in(__DIR__.'/templates')
;

return Config::create()
    ->setFinder($finder)
    ->setName('big-desk-config')
;

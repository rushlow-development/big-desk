<?php

namespace App\Twig;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class MarkdownExtension implements ExtensionInterface
{
    #[\Override]
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
    }
}

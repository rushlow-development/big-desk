<?php

namespace App\DataFixtures;

use App\Factory\NoteFactory;
use App\Factory\TodoListFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LorumIpsumFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        NoteFactory::new()->withContent()->createMany(10);
        TodoListFactory::new()->withTasks()->createMany(2);
    }
}

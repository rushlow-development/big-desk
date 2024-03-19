<?php

namespace App\DataFixtures;

use App\Factory\NoteFactory;
use App\Factory\TaskFactory;
use App\Factory\TimeEntryFactory;
use App\Factory\TodoListFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LorumIpsumFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        NoteFactory::new()->withContent()->createMany(10);
        $todoLists = TodoListFactory::createMany(3);

        foreach ($todoLists as $todoList) {
            TaskFactory::createMany(3, ['todoList' => $todoList]);
        }

        TimeEntryFactory::createMany(3);
    }
}

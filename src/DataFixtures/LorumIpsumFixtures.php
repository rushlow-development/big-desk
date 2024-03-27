<?php

namespace App\DataFixtures;

use App\Factory\NoteFactory;
use App\Factory\TaskFactory;
use App\Factory\TimerFactory;
use App\Factory\TodoListFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LorumIpsumFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $user = UserFactory::createOne(['username' => 'testuser']);

        NoteFactory::new(['owner' => $user])->withContent()->createMany(10);
        $todoLists = TodoListFactory::createMany(3, ['owner' => $user]);

        foreach ($todoLists as $todoList) {
            TaskFactory::createMany(3, ['todoList' => $todoList]);
        }

        TimerFactory::new()->notRunning()->createMany(3, ['owner' => $user]);
    }
}

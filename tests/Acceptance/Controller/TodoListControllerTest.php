<?php

namespace App\Tests\Acceptance\Controller;

use App\Tests\PantherTestCase;

class TodoListControllerTest extends PantherTestCase
{
    private string $path = '/todo';

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%s/new', $this->path));

        self::assertPageTitleContains('New TodoList');

        $this->clickButton('#add-task');

        $this->client->waitFor('#todo_list_tasks_0_name');

        $this->client->submitForm('Save', [
            'todo_list[name]' => 'My List',
            'todo_list[tasks][0][name]' => 'new task',
        ]);

        $this->client->waitFor('.task-item');
        $this->assertSelectorCount(1, '.task-item');
        $this->assertAnySelectorTextContains('.task-item', 'new task');
    }
}

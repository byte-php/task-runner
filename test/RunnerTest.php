<?php declare(strict_types=1);
/**
 * Caller
 *
 * @author  Alwynn <alwynn.github@gmail.com>
 * @package byte/task-runner
 */
namespace Byte\Runner;

use PHPUnit\Framework\TestCase;
use Byte\Caller\CallerInterface;

class RunnerTest extends TestCase
{
    public function testInstance()
    {
        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $runner = new Runner($caller);

        $this->assertInstanceOf(RunnerInterface::class, $runner);
    }

    public function testGetListOfTasks()
    {
        $tasks  = ['task1' => 1, 'task2' => 2];
        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $runner = new Runner($caller, $tasks);

        $this->assertSame($runner->tasks(), array_keys($tasks));
    }

    public function testTaskRegistering()
    {
        $name   = 'test';
        $task   = function () {
        };
        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $runner = new Runner($caller);
        $runner->task($name, $task);

        $this->assertContains($name, $runner->tasks());
    }

    /** @expectedException Byte\Runner\Exception\TaskNotDefined */
    public function testRunOnTaskNotDefined()
    {
        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $runner = new Runner($caller);
        $runner->run('test');
    }

    public function testRunDirectTask()
    {
        $task     = 'test';
        $callback = function () {
        };
        $params   = [1, 2, 3];
        $result   = 1;

        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $caller
            ->expects($this->once())
            ->method('call')
            ->with($this->equalTo($callback), $this->equalTo($params))
            ->will($this->returnValue($result));

        $runner = new Runner($caller);
        $runner->task($task, $callback);
        $runner->run($task, $params);
    }

    public function testRunAliasTask()
    {
        $task     = 'test';
        $alias    = 'alias';
        $callback = function () {
        };
        $params   = [1, 2, 3];
        $result   = 1;

        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $caller
            ->expects($this->once())
            ->method('call')
            ->with($this->equalTo($callback), $this->equalTo($params))
            ->will($this->returnValue($result));

        $runner = new Runner($caller);
        $runner->task($task, $callback);
        $runner->task($alias, $task);

        $runner->run($alias, $params);
    }

    public function testRunChainedTasks()
    {
        $task     = 'test';
        $callback1 = function () {
        };
        $callback2 = function () {
        };
        $params1   = [1, 2, 3];
        $result1   = 1;
        $result2   = 2;

        $caller = $this->getMockForAbstractClass(CallerInterface::class);
        $caller->expects($this->at(0))
            ->method('call')
            ->with($this->equalTo($callback1), $this->equalTo($params1))
            ->will($this->returnValue($result1));
        $caller->expects($this->at(1))
            ->method('call')
            ->with($this->equalTo($callback2), $this->equalTo([$result1]))
            ->will($this->returnValue($result2));
        ;

        $runner = new Runner($caller);
        $runner->task($task, $callback1, $callback2);
        $runner->run($task, $params1);
    }
}

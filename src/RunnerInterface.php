<?php declare(strict_types=1);
/**
 * Task runner
 *
 * @author  Alwynn <alwynn.github@gmail.com>
 * @package byte/task-runner
 */
namespace Byte\Runner;

/**
 * Task runner interface
 *
 * @author  Alwynn <alwynn.github@gmail.com>
 * @package byte/task-runner
 */
interface RunnerInterface
{
    /**
     * Register a named task. Can be single task or a list of tasks.
     * Tasks can be callback functions that will be invoked or names
     * of previously registered tasks.
     *
     * @param  string          $name  Task name
     * @param  ...             $tasks Task or callbacks list to call under task name
     * @return RunnerInterface
     */
    public function task(string $name, ... $tasks): RunnerInterface;

    /**
     * Run a task by name
     *
     * @param  string $task       Task name to run
     * @param  array  $parameters Parameters for task to use
     * @return mixed              Output from last executed task
     */
    public function run(string $task, array $parameters = []);

    /**
     * Get a list of tasks
     *
     * @return array Tasks' names
     */
    public function tasks(): array;
}

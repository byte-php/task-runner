<?php declare(strict_types=1);
/**
 * Task runner
 *
 * @author  Alwynn <alwynn.github@gmail.com>
 * @package byte/task-runner
 */
namespace Byte\Runner;

use Byte\Caller\CallerInterface;

/**
 * Task runner.
 *
 * @author  Alwynn <alwynn.github@gmail.com>
 * @package byte/task-runner
 */
final class Runner implements RunnerInterface
{
    /**
     * Caller object that calls registered tasks
     *
     * @var CallerInterface
     */
    protected $caller;

    /**
     * A collection of registered tasks
     *
     * @var array
     */
    protected $tasks;

    /**
     * @param CallerInterface $caller Caller
     * @param array           $tasks  Initial tasks list
     */
    public function __construct(CallerInterface $caller, array $tasks = [])
    {
        $this->caller = $caller;
        $this->tasks  = $tasks;
    }

    /** @inheritdoc */
    public function task(string $name, ... $tasks): RunnerInterface
    {
        $this->tasks[$name] = $tasks;

        return $this;
    }

    /** @inheritdoc */
    public function run(string $task, array $parameters = [])
    {
        if (! isset($this->tasks[$task])) {
            throw new Exception\TaskNotDefined("Task '{$task}' was not registered");
        }

        foreach ($this->tasks[$task] as $callback) {
            $parameters = $this->executeCallback($callback, $parameters);
        }

        // return the result of last iteration
        return $parameters;
    }

    /**
     * Execute a callback retrieved from tasks list
     *
     * @param  mixed  $callback   Task to execute. Can be either a callback to pass to
     *                            caller or a taskname to run
     * @param  array  $parameters Parameters to pass to task callback call
     * @return mixed              Anything that task returns
     */
    private function executeCallback($callback, $parameters = [])
    {
        $parameters = $this->normalizeParameters($parameters);

        // if taskname of existing task, try to run them
        if (is_string($callback) && isset($this->tasks[$callback])) {
            return $this->run($callback, $parameters);
        }

        return $this->caller->call($callback, $parameters);
    }

    /**
     * Normalize parameters for next callback function iteration
     *
     * @param  mixed $parameters Parameters for callback call
     * @return array             Normalized parameters
     */
    private function normalizeParameters($parameters): array
    {
        return is_array($parameters) ? $parameters : [$parameters];
    }

    /** @inheritdoc */
    public function tasks(): array
    {
        return array_keys($this->tasks);
    }
}
